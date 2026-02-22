<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Role;
use App\Sale;
use App\Division;
use App\Branch;
use App\Telemarketing;
use App\TelemarketingDetail;
use App\Source;
use App\User;
use App\SalesAssociate;
use App\Company;

class MonthlyController extends Controller
{
    private function resolvePeriod(?string $month, $year = null): array
    {
        date_default_timezone_set('Asia/Manila');

        $selectedYear = (is_numeric($year) && (int)$year > 0) ? (int)$year : (int)date('Y');
        $selectedMonth = (is_numeric($month) && (int)$month >= 1 && (int)$month <= 12)
            ? str_pad((string)((int)$month), 2, '0', STR_PAD_LEFT)
            : date('m');

        $selectedDate = date('Y-m-d', strtotime($selectedYear . '-' . $selectedMonth . '-01'));

        return [
            'selected_date' => $selectedDate,
            'start_date' => date('Y-m-01', strtotime($selectedDate)),
            'end_date' => date('Y-m-t', strtotime($selectedDate)),
            'selected_year' => $selectedYear,
            'selected_month' => $selectedMonth,
        ];
    }

    public function index() {
        $latestSaleDate = Sale::whereNotNull('date_purchased')
            ->orderBy('date_purchased', 'desc')
            ->value('date_purchased');

        $defaultYear = $latestSaleDate ? date('Y', strtotime($latestSaleDate)) : date('Y');
        $defaultMonth = $latestSaleDate ? date('m', strtotime($latestSaleDate)) : date('m');

        return view('backend.pages.dashboard.monthly', compact('defaultYear', 'defaultMonth'));
    }

    public function get_record($date, Request $request) {
        $period = $this->resolvePeriod($date === "all" ? null : $date, $request->year);
        $selected_date = $period['selected_date'];
        $start_date = $period['start_date'];
        $end_date = $period['end_date'];
        $selected_year = $period['selected_year'];
        $selected_month = (int)$period['selected_month'];

        $first_day = $selected_year . '-01-01';
        $previous_month_count = $selected_month - 1;

        if ($previous_month_count <= 0) {
            $all = 0;
            $ave = 0;
        } else {
            $previous_month_end = date('Y-m-t', strtotime($selected_date . ' - 1 month'));
            $all = Sale::whereBetween('date_purchased', [$first_day, $previous_month_end])->sum('amount');
            $ave = $all / $previous_month_count;
        }

        $daily = Sale::whereBetween('date_purchased',  [$start_date, $end_date])->sum('amount');
        $trans = Sale::whereBetween('date_purchased',  [$start_date, $end_date])->count();

        $amount = $daily - $ave;

        $growth = array(
            'amount' => $amount,
            'percentage' => ($all === 0?0:(($amount) / $ave) * 100)
        );

        $telemarketing = Telemarketing::count();

        $division = Division::withCount(['division as sumAmount' => function($query) use($selected_date) {
            $query->select(DB::raw('SUM(amount) as total_amount'))->whereBetween('date_purchased',  [date('Y-m-01', strtotime($selected_date)),date('Y-m-t', strtotime($selected_date))]);
        }])->orderBy('sumAmount', 'desc')->get();

        $associate = SalesAssociate::withCount(['associate as sumAmount' => function($query) use($selected_date) {
            $query->select(DB::raw('SUM(amount) as total_amount'))->whereBetween('date_purchased',  [date('Y-m-01', strtotime($selected_date)),date('Y-m-t', strtotime($selected_date))]);
        }])->orderBy('sumAmount', 'desc')->get();

        $industry = Company::select('companies.industry', DB::raw('SUM(sales.amount) as sumAmount'))
            ->join('sales', 'sales.company_id', '=', 'companies.id')
            ->whereBetween('sales.date_purchased', [date('Y-m-01', strtotime($selected_date)), date('Y-m-t', strtotime($selected_date))])
            ->where('sales.amount', '>', 0)
            ->whereNotNull('companies.industry')
            ->where('companies.industry', '!=', '')
            ->groupBy('companies.industry')
            ->orderBy('sumAmount', 'desc')
            ->get();

        $agent = User::withCount(['agent as sumAmount' => function($query) use($selected_date) {
            $query->select(DB::raw('SUM(amount) as total_amount'))->whereBetween('date_purchased',  [date('Y-m-01', strtotime($selected_date)),date('Y-m-t', strtotime($selected_date))]);
        }])->where('designation', 'SALES AGENT')->orderBy('sumAmount', 'desc')->get();

        $source = Source::withCount(['source as sumAmount' => function($query) use($selected_date) {
            $query->select(DB::raw('SUM(amount) as total_amount'))->whereBetween('date_purchased',  [date('Y-m-01', strtotime($selected_date)),date('Y-m-t', strtotime($selected_date))]);
        }])->get();

        $calls = array(
            'incomplete' => TelemarketingDetail::whereNotIn('status',['COMPLETED','CANCELLED'])->whereBetween('date', [date('Y-m-01', strtotime($selected_date)),date('Y-m-t', strtotime($selected_date))])->count(),
            'cancelled' => TelemarketingDetail::where('status','CANCELLED')->whereBetween('date', [date('Y-m-01', strtotime($selected_date)),date('Y-m-t', strtotime($selected_date))])->count(),
            'completed' => TelemarketingDetail::where('status','COMPLETED')->whereBetween('date', [date('Y-m-01', strtotime($selected_date)),date('Y-m-t', strtotime($selected_date))])->count(),
        );

        $total_calls = $calls['incomplete'] + $calls['completed'] + $calls['cancelled'];

        return response()->json(compact('daily', 'trans', 'telemarketing', 'calls', 'total_calls',  'source', 'division', 'associate', 'agent', 'industry', 'growth', 'ave', 'selected_year'));
    }

    public function get_daily(Request $request) {
        $period = $this->resolvePeriod($request->date, $request->year);
        $sale = Sale::with('company', 'source', 'details', 'details.item', 'details.brand')
            ->whereBetween('date_purchased', [$period['start_date'], $period['end_date']])
            ->where('amount', '>', 0)
            ->get();

        return response()->json(compact('sale'));
    }

    public function get_calls(Request $request) {
        $period = $this->resolvePeriod($request->date, $request->year);

        if($request->status === "INCOMPLETE") {
            $status = TelemarketingDetail::with('telemarketing', 'telemarketing.company')
                ->whereNotIn('status',['COMPLETED','CANCELLED'])
                ->whereBetween('date',  [$period['start_date'], $period['end_date']])
                ->get();
        }
        else {
            $status = TelemarketingDetail::with('telemarketing', 'telemarketing.company')
                ->where('status', $request->status)
                ->whereBetween('date',  [$period['start_date'], $period['end_date']])
                ->get();
        }

        // if(request()->ajax()) {
        //     return datatables()->of($status)
        //     ->addIndexColumn()
        //     ->make(true);
        // }
        return response()->json(compact('status'));
    }

    public function get_division(Request $request) {
        $period = $this->resolvePeriod($request->date, $request->year);
        $selected_date = $period['selected_date'];

        $branch = Branch::withCount(['sale as sumAmount' => function($query) use($selected_date) {
            $query->select(DB::raw('SUM(amount) as total_amount'))->whereBetween('date_purchased', [date('Y-m-01', strtotime($selected_date)),date('Y-m-t', strtotime($selected_date))]);
        }])->where('division_id', $request->division_id)->orderBy('sumAmount', 'desc')->get();

        return response()->json(compact('branch'));

    }
    
    public function get_branch(Request $request) {
        $period = $this->resolvePeriod($request->date, $request->year);

        $sale = Sale::with('company', 'source', 'details', 'details.item', 'details.brand')
            ->whereBetween('date_purchased',  [$period['start_date'], $period['end_date']])
            ->where('amount', '>', 0)
            ->where('branch_id', $request->branch_id)
            ->get();

        return response()->json(compact('sale'));

    }

    public function get_agent(Request $request) {
        $period = $this->resolvePeriod($request->date, $request->year);
        $sale = Sale::with('company', 'source', 'details', 'details.item', 'details.brand')
            ->whereBetween('date_purchased',  [$period['start_date'], $period['end_date']])
            ->where('amount', '>', 0)
            ->where('user_id', $request->agent)
            ->get();

        return response()->json(compact('sale'));
    }

    public function get_associate(Request $request) {
        $period = $this->resolvePeriod($request->date, $request->year);
        $sale = Sale::with('company', 'source', 'details', 'details.item', 'details.brand')
            ->whereBetween('date_purchased',  [$period['start_date'], $period['end_date']])
            ->where('amount', '>', 0)
            ->where('sales_associate_id', $request->associate)
            ->get();

        return response()->json(compact('sale'));
    }

    public function get_industry(Request $request) {
        $period = $this->resolvePeriod($request->date, $request->year);
        $sale = Company::with(['industry' => function($query) use ($period) {
            $query->where('amount', '>', 0)
                ->whereBetween('date_purchased', [$period['start_date'], $period['end_date']]);
        }, 'industry.company', 'industry.source', 'industry.details', 'industry.details.item', 'industry.details.brand'])
        ->where('industry', $request->industry)
        ->whereHas('industry', function ($query) use ($period) {
            $query->where('amount', '>', 0)
                ->whereBetween('date_purchased', [$period['start_date'], $period['end_date']]);
        })
        ->orderBy('company_name', 'asc')
        ->get();

        return response()->json(compact('sale'));
    }

    public function get_source(Request $request) {
        $period = $this->resolvePeriod($request->date, $request->year);

        if(request()->ajax()) {
            return datatables()->of(
                Sale::with('company', 'source')
                    ->whereBetween('date_purchased', [$period['start_date'], $period['end_date']])
                    ->where('amount', '>', 0)
                    ->where('source_id', $request->associate)
                    ->get()
            )
            ->addIndexColumn()
            ->make(true);
        }
    }
}

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

class AnnualController extends Controller
{
    public function index() {
        return view('backend.pages.dashboard.annual');
    }

    public function get_record($date, $growth) {

        date_default_timezone_set('Asia/Manila');

        $selected_date = '';

        if($date === "all" || $date === "-") {
            $selected_date = date('Y');
        }
        else {
            $selected_date = $date;
        }

        $first_day = $growth;

        $all = Sale::whereYear('date_purchased',  $first_day)->sum('amount');
        // $month = date('m', strtotime($selected_date . ' - 1 month'));
        // $ave = $all/$month;

        $daily = Sale::whereYear('date_purchased',  $selected_date)->sum('amount');
        $trans = Sale::whereYear('date_purchased',  $selected_date)->count();

        $amount = $daily - $all;

        $growth = array(
            'amount' => $amount,
            'percentage' => ($all === 0?0:(($amount) / $all) * 100)
        );

        $telemarketing = Telemarketing::count();

        $division = Division::withCount(['division as sumAmount' => function($query) use($selected_date) {
            $query->select(DB::raw('SUM(amount) as total_amount'))->whereYear('date_purchased',  $selected_date);
        }])->orderBy('sumAmount', 'desc')->get();

        $associate = SalesAssociate::withCount(['associate as sumAmount' => function($query) use($selected_date) {
            $query->select(DB::raw('SUM(amount) as total_amount'))->whereYear('date_purchased',  $selected_date);
        }])->orderBy('sumAmount', 'desc')->get();

        // $industry = Company::withCount(['industry as sumAmount' => function($query) use($selected_date) {
        //     $query->select(DB::raw('SUM(amount) as total_amount'))->whereYear('date_purchased',  $selected_date);
        // }])->groupBy('industry')->orderBy('sumAmount', 'desc')->get();

        $industry = DB::table('companies')
            ->join('sales', 'sales.company_id', '=', 'companies.id')
            ->select('companies.industry', DB::raw('SUM(sales.amount) as sumAmount'))
            ->whereYear('sales.date_purchased',  $selected_date)
            ->groupBy('companies.industry')
            ->orderBy('sumAmount', 'desc')
            ->get();

        $agent = User::withCount(['agent as sumAmount' => function($query) use($selected_date) {
            $query->select(DB::raw('SUM(amount) as total_amount'))->whereYear('date_purchased',  $selected_date);
        }])->where('designation', 'SALES AGENT')->orderBy('sumAmount', 'desc')->get();

        $source = Source::withCount(['source as sumAmount' => function($query) use($selected_date) {
            $query->select(DB::raw('SUM(amount) as total_amount'))->whereYear('date_purchased',  $selected_date);
        }])->get();

        $calls = array(
            'incomplete' => TelemarketingDetail::whereNotIn('status',['COMPLETED','CANCELLED'])->whereYear('date',  $selected_date)->count(),
            'cancelled' => TelemarketingDetail::where('status','CANCELLED')->whereYear('date',  $selected_date)->count(),
            'completed' => TelemarketingDetail::where('status','COMPLETED')->whereYear('date',  $selected_date)->count(),
        );

        $total_calls = $calls['incomplete'] + $calls['completed'] + $calls['cancelled'];

        return response()->json(compact('daily', 'trans', 'telemarketing', 'calls', 'total_calls',  'source', 'division', 'associate', 'agent', 'industry', 'growth', 'all'));
    }

    public function get_daily(Request $request) {
        date_default_timezone_set('Asia/Manila');

        if($request->date === "" || $request->date === null || $request->date === '-') {
            $selected_date = date('Y');
        }
        else {
            $selected_date = $request->date;
        }

        $sale = Sale::with('company', 'source', 'details', 'details.item', 'details.brand')->whereYear('date_purchased',  $selected_date)->where('amount', '>', 0)->get();

        return response()->json(compact('sale'));
    }

    public function get_calls(Request $request) {
        date_default_timezone_set('Asia/Manila');

        if($request->date === "-" || $request->date === null) {
            $selected_date = date('Y');
        }
        else {
            $selected_date = $request->date;
        }

        if($request->status === "INCOMPLETE") {
            $status = TelemarketingDetail::with('telemarketing', 'telemarketing.company')->whereNotIn('status',['COMPLETED','CANCELLED'])->whereYear('date',  $selected_date)->get();
        }
        else {
            $status = TelemarketingDetail::with('telemarketing', 'telemarketing.company')->where('status', $request->status)->whereYear('date',  $selected_date)->get();
        }

        return response()->json(compact('status'));
    }

    public function get_division(Request $request) {
        date_default_timezone_set('Asia/Manila');

        if($request->date === "" || $request->date === null|| $request->date === "-") {
            $selected_date = date('Y');
        }
        else {
            $selected_date = $request->date;
        }

        $branch = Branch::withCount(['sale as sumAmount' => function($query) use($selected_date) {
            $query->select(DB::raw('SUM(amount) as total_amount'))->whereYear('date_purchased',  $selected_date);
        }])->where('division_id', $request->division_id)->orderBy('sumAmount', 'desc')->get();
        
        return response()->json(compact('branch'));

    }
    
    public function get_branch(Request $request) {
        date_default_timezone_set('Asia/Manila');

        if($request->date === "" || $request->date === null|| $request->date === "-") {
            $selected_date = date('Y');
        }
        else {
            $selected_date = $request->date;
        }

        $sale = Sale::with('company', 'source', 'details', 'details.item', 'details.brand')->whereYear('date_purchased',  $selected_date)->where('amount', '>', 0)->where('branch_id', $request->branch_id)->get();

        return response()->json(compact('sale'));

    }

    public function get_agent(Request $request) {
        date_default_timezone_set('Asia/Manila');

        if($request->date === "" || $request->date === null|| $request->date === "-") {
            $selected_date = date('Y');
        }
        else {
            $selected_date = $request->date;
        }

        $sale = Sale::with('company', 'source', 'details', 'details.item', 'details.brand')->whereYear('date_purchased',  $selected_date)->where('amount', '>', 0)->where('user_id', $request->agent)->get();

        return response()->json(compact('sale'));
    }

    public function get_associate(Request $request) {
        date_default_timezone_set('Asia/Manila');

        if($request->date === "" || $request->date === null|| $request->date === "-") {
            $selected_date = date('Y');
        }
        else {
            $selected_date = $request->date;
        }

        $sale = Sale::with('company', 'source', 'details', 'details.item', 'details.brand')->whereYear('date_purchased',  $selected_date)->where('amount', '>', 0)->where('sales_associate_id', $request->associate)->get();

        return response()->json(compact('sale'));
    }

    public function get_industry(Request $request) {
        date_default_timezone_set('Asia/Manila');

        if($request->date === "" || $request->date === null|| $request->date === "-") {
            $selected_date = date('Y');
        }
        else {
            $selected_date = $request->date;
        }

        $sale = Company::with(['industry' => function($query) {
            $query->where('amount', '>', 0);
        }, 'industry.company', 'industry.source', 'industry.details', 'industry.details.item', 'industry.details.brand'])->withCount(['industry as sumAmount' => function($query) use($selected_date) {
            $query->select(DB::raw('SUM(amount) as total_amount'))->whereYear('date_purchased',  $selected_date);
        }])->where('industry', $request->industry)->orderBy('sumAmount', 'desc')->get();

        return response()->json(compact('sale'));
    }

    public function get_source(Request $request) {
        date_default_timezone_set('Asia/Manila');

        if($request->date === "" || $request->date === null|| $request->date === "-") {
            $selected_date = date('Y');
        }
        else {
            $selected_date = $request->date;
        }

        if(request()->ajax()) {
            return datatables()->of(Sale::with('company', 'source')->whereYear('date_purchased',  $selected_date)->where('amount', '>', 0)->where('source_id', $request->associate)->get())
            ->addIndexColumn()
            ->make(true);
        }
    }
}

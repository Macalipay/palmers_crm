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
    public function index() {
        return view('backend.pages.dashboard.monthly');
    }

    public function get_record($date) {

        date_default_timezone_set('Asia/Manila');

        $selected_date = '';

        if($date === "all") {
            $selected_date = date('Y-m-d');
        }
        else {
            $selected_date = date('Y-'.$date.'-d');
        }

        $first_day = date('Y-01-01');

        $all = Sale::whereBetween('date_purchased',  [$first_day, date('Y-m-t', strtotime($selected_date . ' - 1 month'))])->sum('amount');
        $month = date('m', strtotime($selected_date . ' - 1 month'));
        $ave = $all/$month;

        $daily = Sale::whereBetween('date_purchased',  [date('Y-m-01', strtotime($selected_date)),date('Y-m-t', strtotime($selected_date))])->sum('amount');
        $trans = Sale::whereBetween('date_purchased',  [date('Y-m-01', strtotime($selected_date)),date('Y-m-t', strtotime($selected_date))])->count();

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

        $industry = Company::withCount(['industry as sumAmount' => function($query) use($selected_date) {
            $query->select(DB::raw('SUM(amount) as total_amount'))->whereBetween('date_purchased',  [date('Y-m-01', strtotime($selected_date)),date('Y-m-t', strtotime($selected_date))]);
        }])->orderBy('sumAmount', 'desc')->groupBy('industry')->get();

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

        return response()->json(compact('daily', 'trans', 'telemarketing', 'calls', 'total_calls',  'source', 'division', 'associate', 'agent', 'industry', 'growth', 'ave'));
    }

    public function get_daily(Request $request) {
        date_default_timezone_set('Asia/Manila');

        if($request->date === "" || $request->date === null) {
            $selected_date = date('Y-m-d');
        }
        else {
            $selected_date = date('Y-'.$request->date.'-d');
        }

        $sale = Sale::with('company', 'source', 'details', 'details.item', 'details.brand')->whereBetween('date_purchased', [date('Y-m-01', strtotime($selected_date)),date('Y-m-t', strtotime($selected_date))])->where('amount', '>', 0)->get();

        return response()->json(compact('sale'));
    }

    public function get_calls(Request $request) {
        date_default_timezone_set('Asia/Manila');

        if($request->date === "" || $request->date === null) {
            $selected_date = date('Y-m-d');
        }
        else {
            $selected_date = date('Y-'.$request->date.'-d');
        }

        if($request->status === "INCOMPLETE") {
            $status = TelemarketingDetail::with('telemarketing', 'telemarketing.company')->whereNotIn('status',['COMPLETED','CANCELLED'])->whereBetween('date',  [date('Y-m-01', strtotime($selected_date)),date('Y-m-t', strtotime($selected_date))])->get();
        }
        else {
            $status = TelemarketingDetail::with('telemarketing', 'telemarketing.company')->where('status', $request->status)->whereBetween('date',  [date('Y-m-01', strtotime($selected_date)),date('Y-m-t', strtotime($selected_date))])->get();
        }

        // if(request()->ajax()) {
        //     return datatables()->of($status)
        //     ->addIndexColumn()
        //     ->make(true);
        // }
        return response()->json(compact('status'));
    }

    public function get_division(Request $request) {
        date_default_timezone_set('Asia/Manila');

        if($request->date === "" || $request->date === null) {
            $selected_date = date('Y-m-d');
        }
        else {
            $selected_date = date('Y-'.$request->date.'-d');
        }

        $branch = Branch::withCount(['sale as sumAmount' => function($query) use($selected_date) {
            $query->select(DB::raw('SUM(amount) as total_amount'))->whereBetween('date_purchased', [date('Y-m-01', strtotime($selected_date)),date('Y-m-t', strtotime($selected_date))]);
        }])->where('division_id', $request->division_id)->orderBy('sumAmount', 'desc')->get();

        return response()->json(compact('branch'));

    }
    
    public function get_branch(Request $request) {
        date_default_timezone_set('Asia/Manila');

        if($request->date === "" || $request->date === null) {
            $selected_date = date('Y-m-d');
        }
        else {
            $selected_date = date('Y-'.$request->date.'-d');
        }

        $sale = Sale::with('company', 'source', 'details', 'details.item', 'details.brand')->whereBetween('date_purchased',  [date('Y-m-01', strtotime($selected_date)),date('Y-m-t', strtotime($selected_date))])->where('amount', '>', 0)->where('branch_id', $request->branch_id)->get();

        return response()->json(compact('sale'));

    }

    public function get_agent(Request $request) {
        date_default_timezone_set('Asia/Manila');

        if($request->date === "" || $request->date === null) {
            $selected_date = date('Y-m-d');
        }
        else {
            $selected_date = date('Y-'.$request->date.'-d');
        }
        $sale = Sale::with('company', 'source', 'details', 'details.item', 'details.brand')->whereBetween('date_purchased',  [date('Y-m-01', strtotime($selected_date)),date('Y-m-t', strtotime($selected_date))])->where('amount', '>', 0)->where('user_id', $request->agent)->get();

        return response()->json(compact('sale'));
    }

    public function get_associate(Request $request) {
        date_default_timezone_set('Asia/Manila');

        if($request->date === "" || $request->date === null) {
            $selected_date = date('Y-m-d');
        }
        else {
            $selected_date = date('Y-'.$request->date.'-d');
        }
        $sale = Sale::with('company', 'source', 'details', 'details.item', 'details.brand')->whereBetween('date_purchased',  [date('Y-m-01', strtotime($selected_date)),date('Y-m-t', strtotime($selected_date))])->where('amount', '>', 0)->where('sales_associate_id', $request->associate)->get();

        return response()->json(compact('sale'));
    }

    public function get_industry(Request $request) {
        date_default_timezone_set('Asia/Manila');

        if($request->date === "" || $request->date === null) {
            $selected_date = date('Y-m-d');
        }
        else {
            $selected_date = date('Y-'.$request->date.'-d');
        }
        $sale = Company::with(['industry' => function($query) {
            $query->where('amount', '>', 0);
        }, 'industry.company', 'industry.source', 'industry.details', 'industry.details.item', 'industry.details.brand'])->withCount(['industry as sumAmount' => function($query) use($selected_date) {
            $query->select(DB::raw('SUM(amount) as total_amount'))->whereBetween('date_purchased', [date('Y-m-01', strtotime($selected_date)),date('Y-m-t', strtotime($selected_date))]);
        }])->where('industry', $request->industry)->orderBy('sumAmount', 'desc')->get();

        return response()->json(compact('sale'));
    }

    public function get_source(Request $request) {
        date_default_timezone_set('Asia/Manila');

        if($request->date === "" || $request->date === null) {
            $selected_date = date('Y-m-d');
        }
        else {
            $selected_date = date('Y-'.$request->date.'-d');
        }

        if(request()->ajax()) {
            return datatables()->of(Sale::with('company', 'source')->whereBetween('date_purchased', [date('Y-m-01', strtotime($selected_date)),date('Y-m-t', strtotime($selected_date))])->where('amount', '>', 0)->where('source_id', $request->associate)->get())
            ->addIndexColumn()
            ->make(true);
        }
    }
}

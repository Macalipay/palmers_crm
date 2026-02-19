<?php

namespace App\Http\Controllers;

use Auth;
use App\Sale;
use App\TelemarketingDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GeneralController extends Controller
{

    public function dashboard() {
        $currentDate = Carbon::now();
        $firstDay = Carbon::now()->startOfMonth();
        $lastDay = Carbon::now()->endOfMonth();

        $calls = TelemarketingDetail::whereBetween('date',  [$firstDay, $lastDay])->count();
        $incomplete = TelemarketingDetail::whereNotIn('status',['COMPLETED','CANCELLED'])->whereBetween('date',  [$firstDay, $lastDay])->count();
        $cancel = TelemarketingDetail::where('status','CANCELLED')->whereBetween('date',  [$firstDay, $lastDay])->count();
        $complete = TelemarketingDetail::where('status','COMPLETED')->whereBetween('date',  [$firstDay, $lastDay])->count();

        $transaction = Sale::whereBetween('date_purchased',  [$firstDay, $lastDay])->count();
        $sale = Sale::whereBetween('date_purchased',  [$firstDay, $lastDay])->sum('amount');
        $company = Sale::select('company_id', \DB::raw('SUM(amount) as sales_amount'))->with('company')->whereBetween('date_purchased',  [$firstDay, $lastDay])->groupBy('company_id')->orderByDesc('sales_amount')->get();
        $agent = Sale::select('user_id', \DB::raw('SUM(amount) as sales_amount'))->with('user')->whereBetween('date_purchased',  [$firstDay, $lastDay])->groupBy('user_id')->orderByDesc('sales_amount')->get();
        $division = Sale::select('division_id', \DB::raw('SUM(amount) as sales_amount'))->with('division')->whereBetween('date_purchased',  [$firstDay, $lastDay])->groupBy('division_id')->orderByDesc('sales_amount')->get();
        $sales_associate = Sale::select('sales_associate_id', \DB::raw('SUM(amount) as sales_amount'))->with('sales_associate')->whereBetween('date_purchased',  [$firstDay, $lastDay])->groupBy('sales_associate_id')->orderByDesc('sales_amount')->get();

        return view('backend.pages.dashboard.general', compact('calls', 'incomplete', 'cancel', 'complete', 'sale', 'transaction', 'company', 'agent', 'division', 'sales_associate'));
    }

}

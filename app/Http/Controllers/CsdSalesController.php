<?php

namespace App\Http\Controllers;

use Auth;
use App\Sale;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CsdSalesController extends Controller
{
    
    public function dashboard() {
        $currentDate = Carbon::now();

        $sale = Sale::where('branch_id', 1)->sum('amount');
        $source = count(Sale::select('source_id')->where('branch_id', 1)->groupBy('source_id')->get());
        $merchandiser = count(Sale::select('merchandiser_id')->where('branch_id', 1)->groupBy('merchandiser_id')->where('merchandiser_id', '!=', null)->get());
        $sales_associates = count(Sale::select('sales_associate_id')->where('branch_id', 1)->groupBy('sales_associate_id')->get());
        $company = Sale::select('company_id', \DB::raw('SUM(amount) as sales_amount'))->with('company')->where('branch_id', 1)->groupBy('company_id')->orderByDesc('sales_amount')->limit(10)->get();
        $store = Sale::select('store_id', \DB::raw('SUM(amount) as sales_amount'))->with('store')->where('branch_id', 1)->groupBy('store_id')->orderByDesc('sales_amount')->limit(10)->get();

        return view('backend.pages.dashboard.csd', compact('sale', 'source', 'merchandiser', 'sales_associates', 'company', 'store'));

    }

    public function getFilteredDashboard(Request $request) {
        $currentDate = Carbon::now();
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $sale = Sale::where('branch_id', 1);
        $source_q = Sale::select('source_id')->where('branch_id', 1);
        $merchandise_q = Sale::select('merchandiser_id')->where('branch_id', 1)->where('merchandiser_id', '!=', null);
        $sales_associates_q = Sale::select('sales_associate_id')->where('branch_id', 1);
        $company = Sale::select('company_id', \DB::raw('SUM(amount) as sales_amount'))->with('company')->where('branch_id', 1);
        $store = Sale::select('store_id', \DB::raw('SUM(amount) as sales_amount'))->with('store')->where('branch_id', 1);

        if($start_date !== null || $end_date !== null) {
            $sale = $sale->whereBetween('date_purchased', [$start_date, $end_date]);
            $source_q = $source_q->whereBetween('date_purchased', [$start_date, $end_date]);
            $merchandise_q = $merchandise_q->whereBetween('date_purchased', [$start_date, $end_date]);
            $sales_associates_q = $sales_associates_q->whereBetween('date_purchased', [$start_date, $end_date]);
            $company = $company->whereBetween('date_purchased', [$start_date, $end_date]);
            $store = $store->whereBetween('date_purchased', [$start_date, $end_date]);
        }
        
        if($request->merchandiser !== null) {
            $sale = $sale->where('merchandiser_id', $request->merchandiser);
            $source_q = $source_q->where('merchandiser_id', $request->merchandiser);
            $merchandise_q = $merchandise_q->where('merchandiser_id', $request->merchandiser);
            $sales_associates_q = $sales_associates_q->where('merchandiser_id', $request->merchandiser);
            $company = $company->where('merchandiser_id', $request->merchandiser);
            $store = $store->where('merchandiser_id', $request->merchandiser);
        }

        if($request->sales_associate !== null) {
            $sale = $sale->where('sales_associate_id', $request->sales_associate);
            $source_q = $source_q->where('sales_associate_id', $request->sales_associate);
            $merchandise_q = $merchandise_q->where('sales_associate_id', $request->sales_associate);
            $sales_associates_q = $sales_associates_q->where('sales_associate_id', $request->sales_associate);
            $company = $company->where('sales_associate_id', $request->sales_associate);
            $store = $store->where('sales_associate_id', $request->sales_associate);
        }

        if($request->source !== null) {
            $sale = $sale->where('source_id', $request->source);
            $source_q = $source_q->where('source_id', $request->source);
            $merchandise_q = $merchandise_q->where('source_id', $request->source);
            $sales_associates_q = $sales_associates_q->where('source_id', $request->source);
            $company = $company->where('source_id', $request->source);
            $store = $store->where('source_id', $request->source);
        }

        $sale = $sale->sum('amount');
        $source_q = $source_q->groupBy('source_id')->get();
        $source = count($source_q);
        $merchandise_q = $merchandise_q->groupBy('merchandiser_id')->get();
        $merchandiser = count($merchandise_q);
        $sales_associates_q = $sales_associates_q->groupBy('sales_associate_id')->get();
        $sales_associates = count($sales_associates_q);
        $company = $company->groupBy('company_id')->orderByDesc('sales_amount')->limit(10)->get();
        $store = $store->groupBy('store_id')->orderByDesc('sales_amount')->limit(10)->get();

        return response()->json(compact('sale', 'source', 'merchandiser', 'sales_associates', 'company', 'store'));

    }

    public function getFilterBy(Request $request, $type) {
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        switch($type) {
            case "merchandiser":
                $data = Sale::select('merchandiser_id')->with('merchandiser')->where('branch_id', 1)->where('merchandiser_id', '!=', null);

                if($start_date !== null || $end_date !== null) {
                    $data = $data->whereBetween('date_purchased', [$start_date, $end_date]);
                }

                if($request->sales_associate !== null) {
                    $data = $data->where('sales_associate_id', $request->sales_associate);;
                }
                
                if($request->source !== null) {
                    $data = $data->where('source_id', $request->source);
                }

                $data = $data->groupBy('merchandiser_id')->get();
                break;
                
            case "sales_associate":
                $data = Sale::select('sales_associate_id')->with('sales_associate')->where('branch_id', 1)->where('sales_associate_id', '!=', null);

                if($start_date !== null || $end_date !== null) {
                    $data = $data->whereBetween('date_purchased', [$start_date, $end_date]);
                }

                if($request->merchandiser !== null) {
                    $data = $data->where('merchandiser_id', $request->merchandiser);;
                }

                if($request->source !== null) {
                    $data = $data->where('source_id', $request->source);
                }

                $data = $data->groupBy('sales_associate_id')->get();
                break;
                
            case "source":
                $data = Sale::select('source_id')->with('source')->where('branch_id', 1)->where('source_id', '!=', null);

                if($start_date !== null || $end_date !== null) {
                    $data = $data->whereBetween('date_purchased', [$start_date, $end_date]);
                }

                if($request->merchandiser !== null) {
                    $data = $data->where('merchandiser_id', $request->merchandiser);;
                }

                if($request->sales_associate !== null) {
                    $data = $data->where('sales_associate_id', $request->sales_associate);;
                }

                $data = $data->groupBy('source_id')->get();
                break;
        }
        
        if (request()->ajax()) {
            return datatables()->of($data)
            ->addIndexColumn()
            ->make(true);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Sale;
use App\User;
use App\SalesAssociate;
use App\Merchandiser;
use App\Telemarketing;
use App\TelemarketingDetail;
use App\Source;
use App\Division;
use App\Branch;
use App\Company;
use App\Item;
use App\Brand;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Auth;
use App\ActivityLogs;
use Yajra\DataTables\Facades\DataTables;

class OtcSalesController extends Controller
{
    public function index() {
        $currentYear = Carbon::now()->year;
        $currentDate = Carbon::today();

        $startOfYear = Carbon::createFromDate($currentYear, 1, 1)->startOfDay();
        $endOfYear = Carbon::createFromDate($currentYear, 12, 31)->endOfDay();
       
        $this_year_transaction = Sale::where('branch_id', 3)->whereBetween('date_purchased', [$startOfYear, $endOfYear])->count();
        $today_transaction = Sale::where('branch_id', 3)->whereDate('created_at', $currentDate)->count();
        $overall_transaction = Sale::where('branch_id', 3)->count();

        $sources = Source::where('active', 1)->get();
        $sales_agents = User::where('designation', 'SALES AGENT')->get();
        $merchandisers = Merchandiser::get();
        $sales_associates = SalesAssociate::where('active', 1)->get();
        $divisions = Division::where('active', 1)->get();
        $branches = Branch::where('active', 1)->get();
        $companies = Company::where('active', 1)->get();
        $items = Item::where('active', 1)->get();
        $brands = Brand::where('active', 1)->get();
        
        return view('backend.pages.sales.otc', compact('sources', 'merchandisers', 'sales_agents', 'sales_associates', 'divisions', 'branches', 'companies', 'items', 'brands',
                                                        'overall_transaction', 'this_year_transaction', 'today_transaction'));
    }

    public function get() {
        if(request()->ajax()) {
            $sales = Sale::with('company', 'company.province', 'source', 'user', 'sales_associate', 'division', 'branch', 'store')->where('branch_id', 3)->orderBy('id', 'desc');
    
            return DataTables::eloquent($sales)
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function save(Request $request) {
        $validate = $request->validate([
            'company_id' => ['required', 'max:250'],
            'customer_type' => ['nullable', 'max:250'],
            'source_id' => ['max:250'],
            'po_no' => ['max:250'],
            'date_purchased' => ['required', 'max:250'],
            'user_id' => ['max:250'],
            'sales_associate_id' => ['max:250'],
            'merchandiser_id' => ['max:250'],
            'date_posted' => ['max:250'],
            'agreed_delivery_date' => ['max:250'],
            'active' => ['required', 'max:250'],
        ]);

        $request['division_id'] = 1;
        $request['branch_id'] = 3;
        $request['created_by'] = Auth::user()->id;
        $request['updated_by'] = Auth::user()->id;

        $user = Sale::create($request->all());

        $result = (new ActivityLogsController)->save('create', 'Created a sale record with Transaction ID: '.$user->id, $request->ip());


        $retention_record = Telemarketing::where('company_id', $request->company_id)->first();

        if (!$retention_record) {
            $telemarketing = array(
                'company_id' => $request->company_id,
                'lead_status' => 'RETENTION',
                'opportunity_status' => 'DEAL',
                'source_id' => $request->source_id,
                'product_interest' => 'N/A',
                'active' => 1,
            );
    
            Telemarketing::create($telemarketing);
        }
        
        return response()->json(compact('validate'));
    }

    public function edit($id)
    {
        $data = Sale::with('company', 'store')->where('id', $id)->orderBy('id')->firstOrFail();

        $result = (new ActivityLogsController)->save('view', 'Viewed the record with Transaction ID: '.$id, request()->ip());
        return response()->json(compact('data'));
    }


    public function update(Request $request, $id)
    {
        $sale = Sale::findOrFail($id);

        // Store the old data
        $oldData = $sale->toArray();

        // Update the sale with the new data
        $sale->update($request->all());

        // Store the new data
        $newData = $sale->toArray();

        // Exclude 'updated_at' from the changes if it has been updated
        if (isset($oldData['updated_at']) && isset($newData['updated_at']) && $oldData['updated_at'] != $newData['updated_at']) {
            unset($oldData['updated_at']);
            unset($newData['updated_at']);
        }

        // Compare old and new data to find changes
        $changes = array_diff_assoc($newData, $oldData);

        // Construct the log message
        $logMessage = 'Updated the record with Transaction ID: '.$id.'. Changes: ';
        foreach ($changes as $key => $value) {
            $logMessage .= ucfirst($key) . ' from ' . $oldData[$key] . ' to ' . $value . '; ';
        }

        // Save the activity log
        $result = (new ActivityLogsController)->save('update', $logMessage, request()->ip());
        
        return response()->json();
    }

    public function destroy($id)
    {
        $record = Sale::find($id);
        $record->delete();

        $result = (new ActivityLogsController)->save('delete', 'Deleted the record with Transaction ID: '.$id, request()->ip());

        return response()->json();
    }

    public function filter(Request $request) {
        
        $query = Sale::query();
        $query->with('company', 'company.province', 'source', 'user', 'sales_associate', 'division', 'branch', 'store')->orderBy('id', 'desc');

        if ($request->has('company') && !empty($request->company)) {
            $query->where('company_id', $request->company);
        }
        if ($request->has('store') && !empty($request->store)) {
            $query->where('store_id', $request->store);
        }
        if ($request->has('associate') && !empty($request->associate)) {
            $query->where('sales_associate_id', $request->associate);
        }
        if (($request->has('start_date') && !empty($request->start_date)) && $request->has('end_date') && !empty($request->end_date)) {
            $query->wherebetween('date_purchased', [$request->start_date, $request->end_date]);
        }

        $query->where('branch_id', 3);
        if(request()->ajax()) {
            return datatables()->of($query->get())
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function dashboard() {
        $currentDate = Carbon::now();

        $sale = Sale::where('branch_id', 3)->sum('amount');
        $source = count(Sale::select('source_id')->where('branch_id', 3)->groupBy('source_id')->get());
        $merchandiser = count(Sale::select('merchandiser_id')->where('branch_id', 3)->groupBy('merchandiser_id')->where('merchandiser_id', '!=', null)->get());
        $sales_associates = count(Sale::select('sales_associate_id')->where('branch_id', 3)->groupBy('sales_associate_id')->get());
        $company = Sale::select('company_id', \DB::raw('SUM(amount) as sales_amount'))->with('company')->where('branch_id', 3)->groupBy('company_id')->orderByDesc('sales_amount')->limit(10)->get();
        $store = Sale::select('store_id', \DB::raw('SUM(amount) as sales_amount'))->with('store')->where('branch_id', 3)->groupBy('store_id')->orderByDesc('sales_amount')->limit(10)->get();

        return view('backend.pages.dashboard.otc', compact('sale', 'source', 'merchandiser', 'sales_associates', 'company', 'store'));

    }

    public function getFilteredDashboard(Request $request) {
        $currentDate = Carbon::now();
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $sale = Sale::where('branch_id', 3);
        $source_q = Sale::select('source_id')->where('branch_id', 3);
        $merchandise_q = Sale::select('merchandiser_id')->where('branch_id', 3)->where('merchandiser_id', '!=', null);
        $sales_associates_q = Sale::select('sales_associate_id')->where('branch_id', 3);
        $company = Sale::select('company_id', \DB::raw('SUM(amount) as sales_amount'))->with('company')->where('branch_id', 3);
        $store = Sale::select('store_id', \DB::raw('SUM(amount) as sales_amount'))->with('store')->where('branch_id', 3);

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
                $data = Sale::select('merchandiser_id')->with('merchandiser')->where('branch_id', 3)->where('merchandiser_id', '!=', null);

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
                $data = Sale::select('sales_associate_id')->with('sales_associate')->where('branch_id', 3)->where('sales_associate_id', '!=', null);

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
                $data = Sale::select('source_id')->with('source')->where('branch_id', 3)->where('source_id', '!=', null);

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

    public function getDashboard() {
        if (request()->ajax()) {
            return datatables()->of(
                Sale::with('user', 'telemarketing', 'telemarketing.company')
                    ->where('assigned_to', Auth::user()->id)
                    ->where('status', 'COMPLETED')
                    ->take(500) 
                    ->get()
            )
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function getDashboard_range($user, $start_date, $end_date) {
        if (request()->ajax()) {
            return datatables()->of(
               Sale::with('user', 'telemarketing', 'telemarketing.company')
                    ->where('assigned_to', $user)
                    ->where('status', 'COMPLETED')
                    ->whereBetween('date', [$start_date, $end_date])
                    ->get()
            )
            ->addIndexColumn()
            ->make(true);
        }
    }
}

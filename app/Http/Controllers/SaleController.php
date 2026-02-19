<?php

namespace App\Http\Controllers;

use App\Sale;
use App\User;
use App\SalesAssociate;
use App\Merchandiser;
use App\Telemarketing;
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


class SaleController extends Controller
{
    public function index() {
        $currentYear = Carbon::now()->year;
        $currentDate = Carbon::today();

        $startOfYear = Carbon::createFromDate($currentYear, 1, 1)->startOfDay();
        $endOfYear = Carbon::createFromDate($currentYear, 12, 31)->endOfDay();

        $this_year_transaction = Sale::where('branch_id', '!=', 3)->whereBetween('date_purchased', [$startOfYear, $endOfYear])->count();
        $today_transaction = Sale::where('branch_id', '!=', 1)->whereDate('created_at', $currentDate)->count();
        $overall_transaction = Sale::where('branch_id', '!=', 1)->count();

        $sources = Source::where('active', 1)->get();
        $sales_agents = User::where('designation', 'SALES AGENT')->get();
        $merchandisers = Merchandiser::get();
        $sales_associates = SalesAssociate::where('active', 1)->get();
        $divisions = Division::where('active', 1)->get();
        $branches = Branch::where('active', 1)->get();
        $companies = Company::where('active', 1)->get();
        $items = Item::where('active', 1)->get();
        $brands = Brand::where('active', 1)->get();
        return view('backend.pages.sales.sales', compact('sources', 'merchandisers', 'sales_agents', 'sales_associates', 'divisions', 'branches', 'companies', 'items', 'brands',
                                                        'overall_transaction', 'this_year_transaction', 'today_transaction'));
    }

    
    public function otcDashboard() {
        return view ('backend.pages.dashboard.otc');
    }

    public function get() {
        if(request()->ajax()) {
            $sales = Sale::with('company', 'company.province', 'source', 'user', 'sales_associate', 'division', 'branch', 'store')->where('branch_id', '!=', 3)->orderBy('id', 'desc');
    
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
            'division_id' => ['max:250'],
            'merchandiser_id' => ['max:250'],
            'branch_id' => ['max:250'],
            'date_posted' => ['max:250'],
            'agreed_delivery_date' => ['max:250'],
            'active' => ['required', 'max:250'],
        ]);

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

        $oldData = $sale->toArray();

        $sale->update($request->all());

        $newData = $sale->toArray();

        if (isset($oldData['updated_at']) && isset($newData['updated_at']) && $oldData['updated_at'] != $newData['updated_at']) {
            unset($oldData['updated_at']);
            unset($newData['updated_at']);
        }

        $changes = array_diff_assoc($newData, $oldData);

        $logMessage = 'Updated the record with Transaction ID: '.$id.'. Changes: ';
        foreach ($changes as $key => $value) {
            $logMessage .= ucfirst($key) . ' from ' . $oldData[$key] . ' to ' . $value . '; ';
        }

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
       
        $query->where('branch_id', 1);
        if(request()->ajax()) {
            return datatables()->of($query->get())
            ->addIndexColumn()
            ->make(true);
        }
    }
}

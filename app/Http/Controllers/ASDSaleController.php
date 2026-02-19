<?php

namespace App\Http\Controllers;

use Auth;
use App\ASDSale;
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
use App\Personnel;
use App\ProductSystemSetup;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ASDSaleController extends Controller
{
    public function index() {
        $currentYear = Carbon::now()->year;
        $currentDate = Carbon::today();

        $startOfYear = Carbon::createFromDate($currentYear, 1, 1)->startOfDay();
        $endOfYear = Carbon::createFromDate($currentYear, 12, 31)->endOfDay();

       if (Auth::user()->designation == 'SUPER ADMIN' || Auth::user()->designation == 'Super Admin') {
            $this_year_transaction = ASDSale::whereBetween('date_purchased', [$startOfYear, $endOfYear])->count();
            $today_transaction = ASDSale::whereDate('created_at', $currentDate)->count();
            $overall_transaction = ASDSale::count();
       } else {
            $this_year_transaction = ASDSale::whereBetween('date_purchased', [$startOfYear, $endOfYear])->count();
            $today_transaction = ASDSale::whereDate('created_at', $currentDate)->count();
            $overall_transaction = ASDSale::count();
       }

        $sources = Source::where('active', 1)->get();
        $sales_agents = User::where('designation', 'SALES AGENT')->get();
        $merchandisers = Merchandiser::get();
        $sales_associates = SalesAssociate::where('active', 1)->get();
        $divisions = Division::where('active', 1)->get();
        $branches = Branch::where('active', 1)->get();
        $companies = Company::where('active', 1)->get();
        $items = Item::where('active', 1)->get();
        $brands = Brand::where('active', 1)->get();

        $supervisor = Personnel::where('position', 'Design & Supervisor')->where('type', 1)->get();
        $design = Personnel::where('position', 'Design & Estimate')->where('type', 1)->get();
        $sales = Personnel::where('position', 'Sales In Charge')->where('type', 1)->get();

        return view('backend.pages.sales.asd', compact('sources', 'merchandisers', 'sales_agents', 'sales_associates', 'divisions', 'branches', 'companies', 'items', 'brands',
                                                        'overall_transaction', 'this_year_transaction', 'today_transaction', 'supervisor', 'design', 'sales'));
    }
    
    public function get() {
        if(Auth::user()->designation == 'SUPER ADMIN' || Auth::user()->designation == 'Super Admin') {
            if(request()->ajax()) {
                return datatables()->of(ASDSale::with('sales', 'design', 'supervisor', 'source')->where('type', 1)->orderBy('id', 'desc')->limit(500)->get())
                ->addIndexColumn()
                ->make(true);
            }
        } else {
            if(request()->ajax()) {
                return datatables()->of(ASDSale::with('sales', 'design', 'supervisor', 'source')->where('type', 1)->orderBy('id', 'desc')->limit(500)->get())
                ->addIndexColumn()
                ->make(true);
            }
        }
    }

    public function save(Request $request) {
        $validate = $request->validate([
            'rfq_no' => ['required', 'max:250'],
            'source_id' => ['nullable', 'max:250'],
            'category' => ['max:250'],
            'company_type' => ['max:250'],
            'project_title' => ['required', 'max:250'],
            'company_name' => ['max:250'],
            'company_address' => ['max:250'],
            'contact_person' => ['max:250'],
            'designation' => ['max:250'],
            'telephone' => ['max:250'],
            'email' => ['max:250'],
            'date_received' => ['max:250'],
            'date_filed' => ['required', 'max:250'],
            'project_location' => ['required', 'max:250'],
            'tcp' => ['required', 'max:250'],
            'deadline' => ['required', 'max:250'],
            'sales_associate_id' => ['required', 'max:250'],
            'design_id' => ['required', 'max:250'],
            'supervisor' => ['required', 'max:250'],
            'date_submitted' => ['required', 'max:250'],
            'quoted_amount' => ['required', 'max:250'],
            'reference_no' => ['required', 'max:250'],
            // 'date_purchased' => ['required', 'max:250'],
            // 'po_no' => ['required', 'max:250'],
            // 'po_amount' => ['required', 'max:250'],
        ]);

        $request['created_by'] = Auth::user()->id;
        $request['updated_by'] = Auth::user()->id;

        $user = ASDSale::create($request->except('product_record'));

        if($request->product_record !== null) {
            foreach($request->product_record as $item) {
                $data = [
                    "sales_id" => $user->id,
                    "product" => $item['product'],
                    "type" => $item['type'],
                    "indx" => $item['indx'],
                    "other_value" => $item['other_value'],
                ];

                ProductSystemSetup::create($data);
            }
        }

        $result = (new ActivityLogsController)->save('create', 'Created a ASD sale record with Transaction ID: '.$user->id, $request->ip());


        // $retention_record = Telemarketing::where('company_id', $request->company_id)->first();

        // if (!$retention_record) {
        //     $telemarketing = array(
        //         'company_id' => $request->company_id,
        //         'lead_status' => 'RETENTION',
        //         'opportunity_status' => 'DEAL',
        //         'source_id' => $request->source_id,
        //         'product_interest' => 'N/A',
        //         'active' => 1,
        //     );
    
        //     Telemarketing::create($telemarketing);
        // }
        
        return response()->json(compact('validate'));
    }

    public function edit($id)
    {
        $data = ASDSale::with('setup')->where('id', $id)->orderBy('id')->firstOrFail();

        $result = (new ActivityLogsController)->save('view', 'Viewed the record with Transaction ID: '.$id, request()->ip());
        return response()->json(compact('data'));
    }

    public function update(Request $request, $id)
    {
        $sale = ASDSale::findOrFail($id);

        // Store the old data
        $oldData = $sale->toArray();


        // Update the sale with the new data
        $sale->update($request->except('product_record'));
        ProductSystemSetup::where('sales_id', $id)->delete();
        
        if($request->product_record !== null) {
            foreach($request->product_record as $item) {
                $data = [
                    "sales_id" => $id,
                    "product" => $item['product'],
                    "type" => $item['type'],
                    "indx" => $item['indx'],
                    "other_value" => $item['other_value'],
                ];

                ProductSystemSetup::create($data);
            }
        }

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
        $record = ASDSale::find($id);
        $record->delete();

        ProductSystemSetup::where('sales_id', $id)->delete();
        
        $result = (new ActivityLogsController)->save('delete', 'Deleted the record with Transaction ID: '.$id, request()->ip());

        return response()->json();
    }

    public function filter(Request $request) {
        
        $query = ASDSale::query();
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

        if(Auth::user()->designation == 'SUPER ADMIN' || Auth::user()->designation == 'Super Admin') {
            if(request()->ajax()) {
                return datatables()->of($query->get())
                ->addIndexColumn()
                ->make(true);
            }
        } else {
            $query->where('branch_id', Auth::user()->branch_id);
            if(request()->ajax()) {
                return datatables()->of($query->get())
                ->addIndexColumn()
                ->make(true);
            }
        }
    }
}

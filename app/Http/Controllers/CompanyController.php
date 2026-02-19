<?php

namespace App\Http\Controllers;

use App\Company;
use App\ProvinceName;
use Illuminate\Http\Request;
use App\Http\Controllers\ActivityLogsController;
use Yajra\DataTables\Facades\DataTables;

use Auth;

class CompanyController extends Controller
{
    public function index() {
        $provinces = ProvinceName::where('active', 1)->get();
        return view('backend.pages.company.company', compact('provinces'));
    }

    public function get() {
        if (request()->ajax()) {
            $companies = Company::with('province')->orderBy('id', 'desc');
    
            return DataTables::eloquent($companies)
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function save(Request $request) {
        $validate = $request->validate([
            'company_name' => ['required', 'max:250'],
            'contact_person' => ['required', 'max:250'],
            'contact_no' => ['required', 'max:250'],
            'address' => ['required', 'max:250'],
            'province_id' => ['required', 'max:250'],
            'industry' => ['required', 'max:250'],
        ]);

        $request['created_by'] = Auth::user()->id;
        $request['updated_by'] = Auth::user()->id;

        $user = Company::create($request->all());

        $result = (new ActivityLogsController)->save('create', 'Created a company record with Company Name: '.$user->company_name, $request->ip());

        return response()->json(compact('validate'));
    }

    public function edit($id)
    {
        $data = Company::where('id', $id)->orderBy('id')->firstOrFail();

        $result = (new ActivityLogsController)->save('view', 'Viewed the record with Company Name: '.$data->company_name, request()->ip());
        return response()->json(compact('data'));
    }


    public function update(Request $request, $id)
{
    $company = Company::findOrFail($id);

    // Store the old data
    $oldData = $company->toArray();

    // Update the company with the new data
    $company->update($request->all());

    // Store the new data
    $newData = $company->toArray();

    // Exclude 'updated_at' from the changes if it has been updated
    if (isset($oldData['updated_at']) && isset($newData['updated_at']) && $oldData['updated_at'] != $newData['updated_at']) {
        unset($oldData['updated_at']);
        unset($newData['updated_at']);
    }

    // Compare old and new data to find changes
    $changes = array_diff_assoc($newData, $oldData);

    // Construct the log message
    $logMessage = 'Updated the record with Company Name: '.$company->company_name.'. Changes: ';
    foreach ($changes as $key => $value) {
        $logMessage .= ucfirst($key) . ' from ' . $oldData[$key] . ' to ' . $value . '; ';
    }

    // Save the activity log
    $result = (new ActivityLogsController)->save('update', $logMessage, request()->ip());
    
    return response()->json();
}

    public function destroy($id)
    {
        $record = Company::find($id);
        $record->delete();

        $result = (new ActivityLogsController)->save('delete', 'Deleted the record with Company Name: '.$record->company_name, request()->ip());

        return response()->json();
    }
}

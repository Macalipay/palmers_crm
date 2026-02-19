<?php

namespace App\Http\Controllers;

use App\Company;
use App\Store;
use Illuminate\Http\Request;
use App\Http\Controllers\ActivityLogsController;
use Auth;

class StoreController extends Controller
{
    public function index() {
        return view('backend.pages.company.store');
    }

    public function get() {
        if(request()->ajax()) {
            return datatables()->of(Store::with('company')->orderBy('id', 'desc')->get())
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function store_list($id) 
    {
        if(request()->ajax()) {
            return datatables()->of(Store::with('company')->where('company_id', $id)->get())
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function all_Store() 
    {
        if(request()->ajax()) {
            return datatables()->of(Store::with('company')->get())
            ->addIndexColumn()
            ->make(true);
        }
    }
    public function save(Request $request) {
        $validate = $request->validate([
            'company_id' => ['required', 'max:250'],
            'code' => ['nullable', 'max:250'],
            'store_name' => ['nullable', 'max:250'],
            'contact' => ['nullable', 'max:250'],
            'address' => ['nullable', 'max:250'],
            'remarks' => ['nullable', 'max:250'],
        ]);

        $request['created_by'] = Auth::user()->id;
        $request['updated_by'] = Auth::user()->id;

        $user = Store::create($request->all());

        $result = (new ActivityLogsController)->save('create', 'Created a company record with Store Name: '.$user->company_name, $request->ip());

        return response()->json(compact('validate'));
    }

    public function edit($id)
    {
        $data = Store::with('company')->where('id', $id)->orderBy('id')->firstOrFail();

        $result = (new ActivityLogsController)->save('view', 'Viewed the record with Store Name: '.$data->company_name, request()->ip());
        return response()->json(compact('data'));
    }


    public function update(Request $request, $id)
{
    $company = Store::findOrFail($id);

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
    $logMessage = 'Updated the record with Store Name: '.$company->company_name.'. Changes: ';
    foreach ($changes as $key => $value) {
        $logMessage .= ucfirst($key) . ' from ' . $oldData[$key] . ' to ' . $value . '; ';
    }

    // Save the activity log
    $result = (new ActivityLogsController)->save('update', $logMessage, request()->ip());
    
    return response()->json();
}

    public function destroy($id)
    {
        $record = Store::find($id);
        $record->delete();

        $result = (new ActivityLogsController)->save('delete', 'Deleted the record with Store Name: '.$record->company_name, request()->ip());

        return response()->json();
    }
}

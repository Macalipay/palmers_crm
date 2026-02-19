<?php

namespace App\Http\Controllers;

use App\Brand;
use Illuminate\Http\Request;
use Auth;
use App\ActivityLogs;

class BrandController extends Controller
{
    public function index() {
        return view('backend.pages.maintenance.brand');
    }

    public function get() {
        if(request()->ajax()) {
            return datatables()->of(Brand::get())
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function save(Request $request) {
        $validate = $request->validate([
            'brand' => ['required', 'max:250'],
            'active' => ['required', 'max:250'],
        ]);

        $request['created_by'] = Auth::user()->id;
        $request['updated_by'] = Auth::user()->id;

        $user = Brand::create($request->all());

        $result = (new ActivityLogsController)->save('create', 'Created a record with Brand Name: '.$user->brand, $request->ip());

        return response()->json(compact('validate'));
    }

    public function edit($id)
    {
        $data = Brand::where('id', $id)->orderBy('id')->firstOrFail();

        $result = (new ActivityLogsController)->save('view', 'Viewed a record with Brand Name: '.$data->brand, request()->ip());

        return response()->json(compact('data'));
    }


    public function update(Request $request, $id)
{
    $brand = Brand::findOrFail($id);

    // Store the old data
    $oldData = $brand->toArray();

    // Update the brand with the new data
    $brand->update($request->all());

    // Store the new data
    $newData = $brand->toArray();

    // Exclude 'updated_at' from the changes if it has been updated
    if (isset($oldData['updated_at']) && isset($newData['updated_at']) && $oldData['updated_at'] != $newData['updated_at']) {
        unset($oldData['updated_at']);
        unset($newData['updated_at']);
    }

    // Compare old and new data to find changes
    $changes = array_diff_assoc($newData, $oldData);

    // Construct the log message
    $logMessage = 'Updated a record with Brand Name: '.$brand->brand.'. Changes: ';
    foreach ($changes as $key => $value) {
        $logMessage .= ucfirst($key) . ' from ' . $oldData[$key] . ' to ' . $value . '; ';
    }

    // Save the activity log
    $result = (new ActivityLogsController)->save('update', $logMessage, request()->ip());
    
    return response()->json();
}

    public function destroy($id)
    {
        $record = Brand::find($id);
        $record->delete();

        $result = (new ActivityLogsController)->save('delete', 'Deleted a record with Brand Name: '.$record->brand, request()->ip());

        return response()->json();
    }
}

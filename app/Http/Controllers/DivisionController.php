<?php

namespace App\Http\Controllers;

use App\Division;
use Illuminate\Http\Request;
use Auth;
use App\ActivityLogs;

class DivisionController extends Controller
{
    public function index() {
        return view('backend.pages.maintenance.division');
    }

    public function get() {
        if(request()->ajax()) {
            return datatables()->of(Division::get())
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function save(Request $request) {
        $validate = $request->validate([
            'division' => ['required', 'max:250'],
            'active' => ['required', 'max:250'],
        ]);

        $request['created_by'] = Auth::user()->id;
        $request['updated_by'] = Auth::user()->id;

        $user = Division::create($request->all());

        $result = (new ActivityLogsController)->save('create', 'Created a record with Division Name: '.$user->division, $rqeuest->ip());

        return response()->json(compact('validate'));
    }

    public function edit($id)
    {
        $data = Division::where('id', $id)->orderBy('id')->firstOrFail();

        $result = (new ActivityLogsController)->save('view', 'Viewed a record with Division Name: '.$data->division, request()->ip());

        return response()->json(compact('data'));
    }


    public function update(Request $request, $id)
    {
        $division = Division::findOrFail($id);
    
        // Store the old data
        $oldData = $division->toArray();
    
        // Update the division with the new data
        $division->update($request->all());
    
        // Store the new data
        $newData = $division->toArray();
    
        // Exclude 'updated_at' from the changes if it has been updated
        if (isset($oldData['updated_at']) && isset($newData['updated_at']) && $oldData['updated_at'] != $newData['updated_at']) {
            unset($oldData['updated_at']);
            unset($newData['updated_at']);
        }
    
        // Compare old and new data to find changes
        $changes = array_diff_assoc($newData, $oldData);
    
        // Construct the log message
        $logMessage = 'Updated a record with Division Name: '.$division->division.'. Changes: ';
        foreach ($changes as $key => $value) {
            $logMessage .= ucfirst($key) . ' from ' . $oldData[$key] . ' to ' . $value . '; ';
        }
    
        // Save the activity log
        $result = (new ActivityLogsController)->save('update', $logMessage, request()->ip());
        
        return response()->json();
    }

    public function destroy($id)
    {
        $record = Division::find($id);
        $record->delete();

        $result = (new ActivityLogsController)->save('delete', 'Deleted a record with Division Name: '.$record->division, request()->ip());

        return response()->json();
    }
}

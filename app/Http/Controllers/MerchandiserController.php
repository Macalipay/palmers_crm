<?php

namespace App\Http\Controllers;

use App\Merchandiser;
use Illuminate\Http\Request;
use Auth;
use App\ActivityLogs;


class MerchandiserController extends Controller
{
    public function index() {
        return view('backend.pages.maintenance.merchandiser');
    }

    public function get() {
        if(request()->ajax()) {
            return datatables()->of(Merchandiser::get())
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function save(Request $request) {
        $validate = $request->validate([
            'merchandiser' => ['required', 'max:250'],
            'active' => ['required', 'max:250'],
        ]);

        $request['created_by'] = Auth::user()->id;
        $request['updated_by'] = Auth::user()->id;

        $user = Merchandiser::create($request->all());

        $result = (new ActivityLogsController)->save('create', 'Created a record with Sales Associate Name: '.$user->merchandiser, $request->ip());


        return response()->json(compact('validate'));
    }

    public function edit($id)
    {
        $data = Merchandiser::where('id', $id)->orderBy('id')->firstOrFail();

        $result = (new ActivityLogsController)->save('view', 'Viewed a record with Sales Associate Name: '.$data->merchandiser, request()->ip());

        return response()->json(compact('data'));
    }


    public function update(Request $request, $id)
    {
        $salesAssociate = Merchandiser::findOrFail($id);
    
        // Store the old data
        $oldData = $salesAssociate->toArray();
    
        // Update the sales associate with the new data
        $salesAssociate->update($request->all());
    
        // Store the new data
        $newData = $salesAssociate->toArray();
    
        // Exclude 'updated_at' from the changes if it has been updated
        if (isset($oldData['updated_at']) && isset($newData['updated_at']) && $oldData['updated_at'] != $newData['updated_at']) {
            unset($oldData['updated_at']);
            unset($newData['updated_at']);
        }
    
        // Compare old and new data to find changes
        $changes = array_diff_assoc($newData, $oldData);
    
        // Construct the log message
        $logMessage = 'Updated a record with Sales Associate Name: '.$salesAssociate->merchandiser.'. Changes: ';
        foreach ($changes as $key => $value) {
            $logMessage .= ucfirst($key) . ' from ' . $oldData[$key] . ' to ' . $value . '; ';
        }
    
        // Save the activity log
        $result = (new ActivityLogsController)->save('update', $logMessage, request()->ip());
        
        return response()->json();
    }

    public function destroy($id)
    {
        $record = Merchandiser::find($id);
        $record->delete();

        $result = (new ActivityLogsController)->save('delete', 'Deleted a record with Sales Associate Name: '.$record->merchandiser, request()->ip());

        return response()->json();
    }
}

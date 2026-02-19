<?php

namespace App\Http\Controllers;

use App\Source;
use Illuminate\Http\Request;
use Auth;
use App\ActivityLogs;


class SourceController extends Controller
{
    public function index() {
        return view('backend.pages.maintenance.source');
    }

    public function get() {
        if(request()->ajax()) {
            return datatables()->of(Source::get())
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function save(Request $request) {
        $validate = $request->validate([
            'source' => ['required', 'max:250'],
            'active' => ['required', 'max:250'],
        ]);

        $request['created_by'] = Auth::user()->id;
        $request['updated_by'] = Auth::user()->id;

        $user = Source::create($request->all());

        $result = (new ActivityLogsController)->save('create', 'Created a record with Source Name: '.$user->source, $request->ip());

        return response()->json(compact('validate'));
    }

    public function edit($id)
    {
        $data = Source::where('id', $id)->orderBy('id')->firstOrFail();

        $result = (new ActivityLogsController)->save('view', 'Viewed a record with Source Name: '.$data->source, request()->ip());
        return response()->json(compact('data'));
    }


    public function update(Request $request, $id)
    {
        $source = Source::findOrFail($id);

        // Store the old data
        $oldData = $source->toArray();

        // Update the source with the new data
        $source->update($request->all());

        // Store the new data
        $newData = $source->toArray();

        // Exclude 'updated_at' from the changes if it has been updated
        if (isset($oldData['updated_at']) && isset($newData['updated_at']) && $oldData['updated_at'] != $newData['updated_at']) {
            unset($oldData['updated_at']);
            unset($newData['updated_at']);
        }

        // Compare old and new data to find changes
        $changes = array_diff_assoc($newData, $oldData);

        // Construct the log message
        $logMessage = 'Updated a record with Source Name: '.$source->source.'. Changes: ';
        foreach ($changes as $key => $value) {
            $logMessage .= ucfirst($key) . ' from ' . $oldData[$key] . ' to ' . $value . '; ';
        }

        // Save the activity log
        $result = (new ActivityLogsController)->save('update', $logMessage, request()->ip());
        
        return response()->json();
    }

    public function destroy($id)
    {
        $record = Source::find($id);
        $record->delete();

        $result = (new ActivityLogsController)->save('delete', 'Deleted a record with Source Name: '.$record->source, request()->ip());

        return response()->json();
    }
}

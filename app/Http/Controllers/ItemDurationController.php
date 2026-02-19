<?php

namespace App\Http\Controllers;

use App\ItemDuration;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\ActivityLogs;


class ItemDurationController extends Controller
{
    public function save(Request $request) {
        
        $validate = $request->validate([
            'brandnew' => ['required', 'max:250'],
            'refill' => ['required', 'max:250'],
            'for_warranty' => ['required', 'max:250'],
        ]);

        $request['created_by'] = Auth::user()->id;
        $request['updated_by'] = Auth::user()->id;

        return response()->json(compact('validate'));
    }

    public function edit($id)
    {
        $data = ItemDuration::where('id', $id)->orderBy('id')->first();

        if ($data !== null) {

            $result = (new ActivityLogsController)->save('view', 'Viewed the record with Item Duration ID: '.$id, request()->ip());

            return response()->json(compact('data'));
        } else {
            return response()->json(['message' => 'NO DATA'], 200);
        }
        
    }

    public function item($id)
    {
        $data = Item::where('id', $id)->orderBy('id')->firstOrFail();
        return response()->json(compact('data'));
    }

    public function update(Request $request, $id)
    {
        $item_duration = ItemDuration::where('item_id', $id)->count();

        if($item_duration > 0) {
            ItemDuration::find($id)->update($request->all());
            $result = (new ActivityLogsController)->save('update', 'Updated the record with Item Duration ID: '.$id, request()->ip());
        }
        else {
            ItemDuration::create($request->all());
            $result = (new ActivityLogsController)->save('update', 'Created the record with Item Duration ID: '.$id, request()->ip());
        }


        return response()->json();
    }

    public function destroy($id)
    {
        $record = ItemDuration::find($id);
        $record->delete();

        $result = (new ActivityLogsController)->save('delete', 'Deleted the record with Item Duration ID: '.$id, request()->ip());

        return response()->json();
    }
}

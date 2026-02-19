<?php

namespace App\Http\Controllers;

use App\Item;
use App\ItemDuration;
use App\Division;
use Illuminate\Http\Request;
use App\ActivityLogs;
use Auth;

class ItemController extends Controller
{
    public function index() {
        $divisions = Division::get();
        return view('backend.pages.item.item', compact('divisions'));
    }

    public function get() {
        if(request()->ajax()) {
            return datatables()->of(Item::with('division')->get())
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function save(Request $request) {
        $validate = $request->validate([
            'item_name' => ['required', 'max:250'],
            'description' => ['required', 'max:250'],
            'division_id' => ['required', 'max:250'],
            'amount' => ['required', 'max:250'],
            'active' => ['required', 'max:250'],
        ]);

        $request['created_by'] = Auth::user()->id;
        $request['updated_by'] = Auth::user()->id;

        $item = Item::create($request->all());

        $item_duration = [
            'item_id' => $item->id,
            'brandnew' => 1,
            'refill' => 1,
            'for_warranty' => 1,
            'active' => 'ACTIVE',
            'created_by' => Auth::user()->id,
            'updated_by' => Auth::user()->id,
        ];

        $duration = ItemDuration::create($item_duration);

        $result = (new ActivityLogsController)->save('create', 'Created an item record with Item ID: '.$item->id, $request->ip());

        return response()->json(compact('validate'));
    }

    public function edit($id)
    {
        $data = Item::where('id', $id)->orderBy('id')->firstOrFail();

        $result = (new ActivityLogsController)->save('view', 'Viewed the record with Item Name: '.$data->item_name, request()->ip());
        return response()->json(compact('data'));
    }


    public function update(Request $request, $id)
    {
        $item = Item::findOrFail($id);
    
        // Store the old data
        $oldData = $item->toArray();
    
        // Update the item with the new data
        $item->update($request->all());
    
        // Store the new data
        $newData = $item->toArray();
    
        // Exclude 'updated_at' from the changes if it has been updated
        if (isset($oldData['updated_at']) && isset($newData['updated_at']) && $oldData['updated_at'] != $newData['updated_at']) {
            unset($oldData['updated_at']);
            unset($newData['updated_at']);
        }
    
        // Compare old and new data to find changes
        $changes = array_diff_assoc($newData, $oldData);
    
        // Construct the log message
        $logMessage = 'Updated the record with Item ID: '.$item->item_name.'. Changes: ';
        foreach ($changes as $key => $value) {
            $logMessage .= ucfirst($key) . ' from ' . $oldData[$key] . ' to ' . $value . '; ';
        }
    
        // Save the activity log
        $result = (new ActivityLogsController)->save('update', $logMessage, request()->ip());
        
        return response()->json();
    }

    public function destroy($id)
    {
        $record = Item::find($id);
        $record->delete();

        $result = (new ActivityLogsController)->save('delete', 'Deleted the record with Item Name: '.$record->item_name, request()->ip());

        return response()->json();
    }

    public function generate_duration()
    {
        $items = Item::get();

        foreach ($items as $item) {
            $existingItemDuration = ItemDuration::where('item_id', $item->id)->first();

            if (!$existingItemDuration) {
                $item_duration = [
                    'item_id' => $item->id,
                    'brandnew' => 1,
                    'refill' => 1,
                    'for_warranty' => 1,
                    'active' => 'ACTIVE',
                    'created_by' => Auth::user()->id,
                    'updated_by' => Auth::user()->id,
                ];
                ItemDuration::create($item_duration);
            }
        }
    }
}

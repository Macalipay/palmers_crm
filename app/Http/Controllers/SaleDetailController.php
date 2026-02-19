<?php

namespace App\Http\Controllers;

use App\SaleDetail;
use App\SalesSerialNo;
use App\Sale;
use App\Item;
use App\ItemDuration;
use App\Telemarketing;
use App\TelemarketingDetail;
use Illuminate\Http\Request;
use Auth;
use App\ActivityLogs;
use App\Jobs\GenerateTaskJob;


class SaleDetailController extends Controller
{
    public function get($id) {
        if(request()->ajax()) {
            $total = SaleDetail::with('item', 'brand')->where('sale_id', $id)->sum('total');
            return datatables()->of(SaleDetail::with('item', 'brand')->where('sale_id', $id)->get())
            ->addIndexColumn()
            ->with('total', $total)
            ->make(true);
        }
    }

    public function save(Request $request) {
        $validate = $request->validate([
            'sale_id' => ['required', 'max:250'],
            'item_id' => ['required', 'max:250'],
            'brand_id' => ['required', 'max:250'],
            'description' => ['required', 'max:250'],
            'warranty_no' => ['max:250'],
            'serial_no' => ['max:250'],
            'quantity' => ['required', 'max:250'],
            'amount' => ['required', 'max:250'],
            'discount' => ['required', 'max:250'],
            'total' => ['required', 'max:250']
        ]);

        $request['created_by'] = Auth::user()->id;
        $request['updated_by'] = Auth::user()->id;

        $sale_detail = SaleDetail::create($request->all());
        $sale_detail_id = $sale_detail->id;

        $record = Sale::where('id', $request->sale_id)->firstOrFail();
        $total = $record->amount + $request->total;

        $sum = SaleDetail::where('sale_id', $request->sale_id)->sum('total');
        Sale::find($request->sale_id)->update(['amount' => $sum]);

        $data = array(
            'sale_details_id' => $sale_detail_id,
            'serial_no' => '',
            'warranty_no' => '',
            'created_by' => Auth::user()->id
        );

        for($i = 0; $i < $request->quantity; $i++) {

            SalesSerialNo::create($data);
        }

        // TASK

        $item_name = Item::where('id', $request->item_id)->firstOrFail();
        $item = ItemDuration::where('item_id', $request->item_id)->firstOrFail();
        if ($request->description == 'BRANDNEW') {
            $duration_month = $item->brandnew;
        } else if($request->description == 'REFILL') {
            $duration_month = $item->refill;
        } else {
            $duration_month = $item->for_warranty;
        }

        $telemarketing = Telemarketing::where('company_id', $record->company_id)->first();
        $telemarketingId = $telemarketing->id ?? 1;
        $datePurchased = \Carbon\Carbon::parse($record->date_purchased);
        $followUpDate = $datePurchased->addMonths($duration_month);
        $resultDate = $followUpDate->toDateString();

        $follow_up_date = $record->date_purchased;

        $task = array(
            'telemarketing_id' => $telemarketingId,
            'order_id' => $sale_detail_id,
            'date' => $resultDate,
            'task' => 'FOLLOW UP CLIENT',
            'description' => 'CUSTOMER ORDERED ' . $item_name->item_name . ' ('. $request->description .') last ' . $record->date_purchased,
            'assigned_to' => 1,
            'status' => 'TO DO',
            'branch_id' => $record->branch_id,
            'remarks' => '',
        );

        TelemarketingDetail::create($task);

        return response()->json(compact('validate'));
    }

    public function edit($id)
    {
        $data = SaleDetail::where('id', $id)->orderBy('id')->firstOrFail();

        $result = (new ActivityLogsController)->save('view', 'Viewed the record with Sale Detail ID: '. $id . ' in Transaction ID: '. $data->sale_id, request()->ip());
        return response()->json(compact('data'));
    }

    public function item($id)
    {
        $data = Item::where('id', $id)->orderBy('id')->firstOrFail();
        return response()->json(compact('data'));
    }

    public function update(Request $request, $id)
{
    $saleDetail = SaleDetail::findOrFail($id);

    // Store the old data
    $oldData = $saleDetail->toArray();

    // Update the sale detail with the new data
    $saleDetail->update($request->all());
    
    $sum = SaleDetail::where('sale_id', $saleDetail->sale_id)->sum('total');
    Sale::find($saleDetail->sale_id)->update(['amount' => $sum]);

    // Store the new data
    $newData = $saleDetail->toArray();

    // Exclude 'updated_at' from the changes if it has been updated
    if (isset($oldData['updated_at']) && isset($newData['updated_at']) && $oldData['updated_at'] != $newData['updated_at']) {
        unset($oldData['updated_at']);
        unset($newData['updated_at']);
    }

    // Compare old and new data to find changes
    $changes = array_diff_assoc($newData, $oldData);

    // Construct the log message
    $logMessage = 'Updated the record with Sale Detail ID: '.$saleDetail->id.' in Transaction ID: '.$saleDetail->sale_id.'. Changes: ';
    foreach ($changes as $key => $value) {
        $logMessage .= ucfirst($key) . ' from ' . $oldData[$key] . ' to ' . $value . '; ';
    }

    // Save the activity log
    $result = (new ActivityLogsController)->save('update', $logMessage, request()->ip());
    
    return response()->json();
}

    public function destroy($id)
    {
        
        $saleDetail = SaleDetail::findOrFail($id)->sale_id;

        SalesSerialNo::where('sale_details_id', $id)->delete();
        $record = SaleDetail::find($id);
        $record->delete();

        $sum = SaleDetail::where('sale_id', $saleDetail)->sum('total');
        Sale::find($saleDetail)->update(['amount' => $sum]);

        $result = (new ActivityLogsController)->save('delete', 'Deleted the record with Sale Detail ID: '.$id, request()->ip());

        return response()->json();
    }

    public function list($id) {
        $record = SaleDetail::where('sale_id', $id)->get();
        return response()->json(compact('record'));
    }

    public function generate_task()
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '1024M');

        $transactions = SaleDetail::offset(21768)->limit(50000)->get();

        foreach ($transactions as $transaction) {
            GenerateTaskJob::dispatch($transaction);
        }

        return response()->json(['message' => 'Tasks queued successfully']);
    }
}

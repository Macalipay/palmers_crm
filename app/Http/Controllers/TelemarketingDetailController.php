<?php

namespace App\Http\Controllers;

use App\TelemarketingDetail;
use App\Calendar;
use App\User;
use App\Sale;
use App\SaleDetail;
use App\TelemarketingCallLog;
use Illuminate\Http\Request;
use Auth;

class TelemarketingDetailController extends Controller
{
    public function get($id) {
        if(request()->ajax()) {
            return datatables()->of(TelemarketingDetail::with('user')->where('telemarketing_id', $id)->get())
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function save(Request $request) {
        $validate = $request->validate([
            'telemarketing_id' => ['required', 'max:250'],
            'date' => ['required', 'max:250'],
            'task' => ['required', 'max:250'],
            'lead_status' => ['required', 'max:250'],
            'description' => ['max:250'],
            'assigned_to' => ['max:250'],
            'status' => ['required', 'max:250'],
            'remarks' => ['nullable', 'max:250'],
        ]);

        $request['created_by'] = Auth::user()->id;
        $request['updated_by'] = Auth::user()->id;

        $record = TelemarketingDetail::create($request->all());

        $user = User::where('id', $request->assigned_to)->first();
        $schedule = [
            'title' => $request->task,
            'telemarketing_detail_id' => $record->id,
            'start' => $request->date,
            'end' => $request->date,
            'color' => 'NAVY BLUE',
            'description' => $request->description,
            'location' => $user->name,
            'reminder' => '',
            'status' => $request->status,
            'created_by' => Auth::user()->id,
            'updated_by' => Auth::user()->id,
        ];

        $calendar = Calendar::create($schedule);

        return response()->json(compact('validate'));
    }

    public function edit($id)
    {
        $data = TelemarketingDetail::with('telemarketing.company', 'csd.sale.sales_associate', 'csd.item', 'csd.item.duration')->where('id', $id)->orderBy('id')->firstOrFail();

        $telemarketing = TelemarketingDetail::where('id', $id)->first();
        $sale_detail = SaleDetail::where('id', $telemarketing->order_id)->first();
        $sale = Sale::where('id', $sale_detail->sale_id)->first();

        $call_logs = TelemarketingCallLog::with('sale', 'telemarketing_detail', 'user')->where('telemarketing_detail_id', $id)->orderBy('created_at')->get();
        
        return response()->json(compact('data', 'call_logs'));
    }

    public function item($id)
    {
        $data = Item::where('id', $id)->orderBy('id')->firstOrFail();
        return response()->json(compact('data'));
    }

    public function update(Request $request, $id)
    {
       $request['assigned_to'] = Auth::user()->id;

        $updated = TelemarketingDetail::find($id)->update($request->all());

        if ($updated) {
            $telemarketing = TelemarketingDetail::where('id', $id)->first();
            $sale_detail = SaleDetail::where('id', $telemarketing->order_id)->first();
            $sale = Sale::where('id', $sale_detail->sale_id)->first();

            $call_log = [
                'sale_id' => $sale->id,
                'telemarketing_detail_id' => $request->telemarketing_detail_id,
                'new_order_id' => $request->new_order_id,
                'total_amount' => $request->total_amount,
                'status' => $request->status,
                'remarks' => $request->remarks,
                'created_by' => Auth::user()->id,
                'updated_by' => Auth::user()->id,
            ];

            TelemarketingCallLog::create($call_log);
            
            return response()->json(['message' => 'Update and log saved successfully.']);
        }

        return response()->json(['error' => 'Failed to update telemarketing detail.'], 500);
    }

    public function destroy($id)
    {
        $record = TelemarketingDetail::find($id);
        $record->delete();

        return response()->json();
    }

    public function list($id) {
        $record = TelemarketingDetail::where('telemarketing_id', $id)->get();
        return response()->json(compact('record'));
    }
}

<?php

namespace App\Http\Controllers;

use App\TelemarketingDetail;
use App\Calendar;
use App\User;
use App\Sale;
use App\SaleDetail;
use App\TelemarketingCallLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
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

    public function companyPofo($companyId)
    {
        if (request()->ajax()) {
            $latestSaleIds = Sale::where('company_id', $companyId)
                ->whereNotNull('po_no')
                ->where('po_no', '!=', '')
                ->selectRaw('MAX(id) as id')
                ->groupBy('po_no');

            $latestTelemarketingBySale = DB::table('telemarketing_details')
                ->join('sale_details', 'telemarketing_details.order_id', '=', 'sale_details.id')
                ->selectRaw('sale_details.sale_id, MAX(telemarketing_details.id) as latest_telemarketing_detail_id')
                ->groupBy('sale_details.sale_id');

            return datatables()->of(
                DB::table('sales')
                    ->leftJoin('users as sales_agents', 'sales.user_id', '=', 'sales_agents.id')
                    ->leftJoin('sales_associates', 'sales.sales_associate_id', '=', 'sales_associates.id')
                    ->leftJoinSub($latestTelemarketingBySale, 'latest_tm', function ($join) {
                        $join->on('latest_tm.sale_id', '=', 'sales.id');
                    })
                    ->leftJoin('telemarketing_details as tm', 'tm.id', '=', 'latest_tm.latest_telemarketing_detail_id')
                    ->leftJoin('users as telemarketers', 'tm.assigned_to', '=', 'telemarketers.id')
                    ->whereIn('sales.id', $latestSaleIds)
                    ->orderBy('sales.date_purchased', 'desc')
                    ->orderBy('sales.id', 'desc')
                    ->select(
                        'sales.id',
                        'sales.po_no',
                        'sales.date_purchased',
                        'sales.amount',
                        'sales_agents.name as sales_agent_name',
                        'sales_associates.sales_associate as sales_associate_name',
                        'telemarketers.name as telemarketer_name'
                    )
            )
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
        $telemarketing = TelemarketingDetail::findOrFail($id);
        $previousStatus = $telemarketing->status;

        $payload = $request->only([
            'new_order_id',
            'total_amount',
            'status',
            'call_duration',
            'remarks',
        ]);
        $payload['assigned_to'] = Auth::user()->id;

        // Guard against schema drift on environments where newer migrations are not yet applied.
        if (!Schema::hasColumn('telemarketing_details', 'call_duration')) {
            unset($payload['call_duration']);
        }

        if (!Schema::hasColumn('telemarketing_details', 'assigned_date')) {
            unset($payload['assigned_date']);
        }

        // Guard against schema drift on environments where the column does not exist yet.
        if (!Schema::hasColumn('telemarketing_details', 'new_order_id')) {
            unset($payload['new_order_id']);
        }

        $updated = $telemarketing->update($payload);

        if ($updated) {
            $newStatus = $request->status;
            $statusChanged = $previousStatus !== $newStatus;

            if ($statusChanged) {
                $sale_detail = SaleDetail::where('id', $telemarketing->order_id)->first();
                $sale = $sale_detail ? Sale::where('id', $sale_detail->sale_id)->first() : null;

                $call_log = [
                    'sale_id' => $sale->id ?? null,
                    'telemarketing_detail_id' => $telemarketing->id,
                    'new_order_id' => $request->new_order_id,
                    'total_amount' => $request->total_amount,
                    'status' => $newStatus,
                    'remarks' => $request->remarks,
                    'created_by' => Auth::user()->id,
                    'updated_by' => Auth::user()->id,
                ];

                TelemarketingCallLog::create($call_log);

                Log::info('Telemarketing status changed', [
                    'telemarketing_detail_id' => $telemarketing->id,
                    'sale_id' => $sale->id ?? null,
                    'changed_by' => Auth::user()->id,
                    'status_from' => $previousStatus,
                    'status_to' => $newStatus,
                    'changed_at' => now()->toDateTimeString(),
                ]);
            }
            
            return response()->json(['message' => 'Update saved successfully.']);
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

<?php

namespace App\Http\Controllers;

use App\TelemarketingDetail;
use App\Company;
use App\User;
use App\Source;
use App\Sale;
use App\SaleDetail;
use App\ItemDuration;
use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;

class TelemarketingController extends Controller
{
    public function index()
    {
        $currentDate = Carbon::now()->toDateString();
        $user = Auth::user();
    
        // Base Query
        $query = TelemarketingDetail::with(['user', 'telemarketing', 'telemarketing.company', 'csd', 'csd.item'])
            ->join('sale_details', 'telemarketing_details.order_id', '=', 'sale_details.id')
            ->join('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->whereHas('csd.item', fn($q) => $q->where('branch_id', 2))
            ->where('sales.branch_id', '!=', 3)
            ->select('telemarketing_details.*', 'telemarketing_details.created_at', 'telemarketing_details.updated_at', 'telemarketing_details.status');

        if (strtoupper($user->designation) === "SUPER ADMIN") {
        } elseif (strtoupper($user->designation) === "TELEMARKETING HEAD") {
            $query->where('sales.branch_id', 3);
        } else {
            $query->where('assigned_to', $user->id);
        }

        // Metrics Queries
        $total_active_call = (clone $query)->where('status', '!=', 'COMPLETED')->count();
        $total_backlogs = (clone $query)->where('status', '!=', 'COMPLETED')->whereDate('telemarketing_details.created_at', '<', $currentDate)->count();
        $total_call_today = (clone $query)->whereDate('telemarketing_details.date', '=', $currentDate)->count();
        $overall_completed_call = (clone $query)->where('status', 'COMPLETED')->whereDate('telemarketing_details.date', '=', $currentDate)->count();
        $completed_call = (clone $query)->where('status', '!=', 'TO DO')
            ->where('assigned_to', $user->id)
            ->whereDate('telemarketing_details.updated_at', '=', $currentDate)
            ->count();

        $telemarketings = User::where('branch_id', 2)->orWhere('id', 1)->get();

        return view('backend.pages.telemarketing.telemarketing', compact(
            'telemarketings', 'total_active_call', 'total_backlogs', 'total_call_today', 'completed_call', 'overall_completed_call'
        ));
    }

    
    public function dashboard()
    {
        $currentDate = Carbon::now();
        $userId = Auth::user()->id;
    
        $statusCounts = TelemarketingDetail::where('assigned_to', $userId)
                        ->selectRaw('status, COUNT(*) as count')
                        ->groupBy('status')
                        ->pluck('count', 'status');
    
        $total_amount = TelemarketingDetail::where('assigned_to', $userId)
                            ->where('status', 'COMPLETED')
                            ->sum('total_amount');
        $formatted_total_amount = '₱' . number_format($total_amount, 2);
    
        $user_todo_call = $statusCounts['TO DO'] ?? 0;
        $user_cancelled_call = $statusCounts['CANCELLED'] ?? 0;
        $user_inprogress_call = $statusCounts['IN PROGRESS'] ?? 0;
        $user_pending_call = $statusCounts['PENDING'] ?? 0;
        $user_onhold_call = $statusCounts['ON-HOLD'] ?? 0;
        $user_completed_call = $statusCounts['COMPLETED'] ?? 0;
    
        $total_active_call = array_sum($statusCounts->except('COMPLETED')->toArray());
    
        $telemarketings = User::where('branch_id', 2)->get();
    
        return view('backend.pages.dashboard.telemarketing', compact(
            'telemarketings', 'user_onhold_call', 'user_pending_call', 'user_inprogress_call',
            'formatted_total_amount', 'user_cancelled_call', 'user_todo_call',
            'total_active_call', 'user_completed_call'
        ));
    }
    

    public function get() {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '1024M');

        if (Auth::user()->designation == "SUPER ADMIN" || Auth::user()->designation == "Super Admin") {
            if (request()->ajax()) {
                return DataTables::eloquent(
                    TelemarketingDetail::with(['user', 'telemarketing', 'telemarketing.company', 'csd', 'csd.item'])
                        ->join('sale_details', 'telemarketing_details.order_id', '=', 'sale_details.id')
                        ->join('sales', 'sale_details.sale_id', '=', 'sales.id') 
                        ->whereHas('csd.item', function ($query) {
                            $query->where('branch_id', 2);
                        })
                        ->where('telemarketing_details.assigned_to', 1)
                        ->where('sales.branch_id', '!=', 3)
                        ->groupBy('sales.company_id')
                        ->select('telemarketing_details.*') 
                )
                ->addIndexColumn()
                ->make(true);
            }
        } else if (Auth::user()->designation == "TELEMARKETING HEAD") {
            if (request()->ajax()) {
                return DataTables::eloquent(
                    TelemarketingDetail::with(['user', 'telemarketing', 'telemarketing.company', 'csd', 'csd.item'])
                        ->join('sale_details', 'telemarketing_details.order_id', '=', 'sale_details.id')
                        ->join('sales', 'sale_details.sale_id', '=', 'sales.id') 
                        ->whereHas('csd.item', function ($query) {
                            $query->where('branch_id', 2);
                        })
                        ->where('telemarketing_details.assigned_to', 1)
                        ->where('sales.branch_id', '!=', 3)
                        ->groupBy('sales.company_id')
                        ->select('telemarketing_details.*') 
                )
                ->addIndexColumn()
                ->make(true);
            }
        } else {
            if (request()->ajax()) {
                return DataTables::eloquent(
                    TelemarketingDetail::with(['user', 'telemarketing', 'telemarketing.company', 'csd', 'csd.item'])
                        ->join('sale_details', 'telemarketing_details.order_id', '=', 'sale_details.id')
                        ->join('sales', 'sale_details.sale_id', '=', 'sales.id') 
                        ->whereHas('csd.item', function ($query) {
                            $query->where('branch_id', 2);
                        })
                        ->where('telemarketing_details.status', 'TO DO')
                        ->where('telemarketing_details.assigned_to', Auth::user()->id)
                        ->where('sales.branch_id', '!=', 3)
                        ->groupBy('sales.company_id')
                        ->select('telemarketing_details.*') 
                )
                ->addIndexColumn()
                ->make(true);
            }
        }
    }

    
    public function getDashboard() {
        if (request()->ajax()) {
            return DataTables::eloquent(
                TelemarketingDetail::with('user', 'telemarketing', 'telemarketing.company')
                    ->where('assigned_to', Auth::user()->id)
                    ->where('status', 'COMPLETED')
            )
            ->addIndexColumn()
            ->make(true);
        }
        
    }

    public function getDashboard_range($user, $start_date, $end_date) {
        if (request()->ajax()) {
            return datatables()->of(
               TelemarketingDetail::with('user', 'telemarketing', 'telemarketing.company')
                    ->where('assigned_to', $user)
                    ->where('status', 'COMPLETED')
                    ->whereBetween('date', [$start_date, $end_date])
                    ->get()
            )
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function status_range($user, $start_date, $end_date)
    {
        $total_amount = TelemarketingDetail::where('assigned_to', $user)
                        ->where('status', 'COMPLETED')
                        ->whereBetween('date', [$start_date, $end_date])
                        ->sum('total_amount');
        $formatted_total_amount = '₱' . number_format($total_amount, 2);

        $total_active_call = TelemarketingDetail::where('assigned_to', $user)->whereBetween('date', [$start_date, $end_date])->count();
        $total_backlogs = TelemarketingDetail::where('assigned_to', $user)->where('status', '!=', 'COMPLETED')->whereBetween('date', [$start_date, $end_date])->count();

        $user_todo_call = TelemarketingDetail::where('assigned_to', $user)->where('status', 'TO DO')->whereBetween('date', [$start_date, $end_date])->count();
        $user_cancelled_call = TelemarketingDetail::where('assigned_to', $user)->where('status', 'CANCELLED')->whereBetween('date', [$start_date, $end_date])->count();
        $user_inprogress_call = TelemarketingDetail::where('assigned_to', $user)->where('status', 'IN PROGRESS')->whereBetween('date', [$start_date, $end_date])->count();
        $user_pending_call = TelemarketingDetail::where('assigned_to', $user)->where('status', 'PENDING')->whereBetween('date', [$start_date, $end_date])->count();
        $user_onhold_call = TelemarketingDetail::where('assigned_to', $user)->where('status', 'ON-HOLD')->whereBetween('date', [$start_date, $end_date])->count();
        $user_completed_call = TelemarketingDetail::where('assigned_to', $user)->where('status', 'COMPLETED')->whereBetween('date', [$start_date, $end_date])->count();

        return response()->json(compact('user_onhold_call', 'user_pending_call', 'user_inprogress_call', 'user_cancelled_call', 'user_todo_call', 'total_active_call', 
                                        'formatted_total_amount', 'total_backlogs', 'user_completed_call'));
    }

    public function save(Request $request) {
        $validate = $request->validate([
            'company_id' => ['required', 'max:250'],
            'lead_status' => ['required', 'max:250'],
            'opportunity_status' => ['max:250'],
            'source_id' => ['max:250'],
            'product_interest' => ['required', 'max:250'],
        ]);

        $request['created_by'] = Auth::user()->id;
        $request['updated_by'] = Auth::user()->id;

        Telemarketing::create($request->all());

        return response()->json(compact('validate'));
    }

    public function edit($id)
    {
        $data = TelemarketingDetail::where('id', $id)->orderBy('id')->firstOrFail();
        return response()->json(compact('data'));
    }

    public function resetDate($id)
    {
        $data = TelemarketingDetail::where('id', $id)->first();
        $sale_detail = SaleDetail::where('id', $data->order_id)->first();
        $itemDuration = ItemDuration::where('item_id', $sale_detail->item_id)->first();
        $sale = Sale::where('id', $sale_detail->sale_id)->first();


        if($sale_detail->description == 'BRANDNEW') {
            $duration = $itemDuration->brandnew;
        } else if ($sale_detail->description == 'REFILL') {
            $duration = $itemDuration->refill;
        } else if ($sale_detail->description == 'FOR WARRANTY'){
            $duration = $itemDuration->for_warranty;
        } else {
            $duration = 6;
        }


        $datePurchased = Carbon::parse($sale->date_purchased);
        $followUpDate = $datePurchased->addMonths($duration);
        $resultDate = $followUpDate->toDateString();

        TelemarketingDetail::find($id)->update(['date'=>$resultDate]);

        return response()->json(compact('data'));
    }

    public function assignedTask(Request $request) {
        foreach ($request->records as $key => $value) {
            $details = TelemarketingDetail::with('csd', 'csd.sale')->where('id', $value)->first();
            TelemarketingDetail::whereHas('csd.sale', function($q) use($details) {
                $q->where('sale_id', $details->csd['sale_id']);
            })->update(['assigned_to' => $request->telemarketing_id, 'assigned_date' => Carbon::now()]);
        }

        return response()->json(['message' => 'success']);
    }

    public function update(Request $request, $id)
    {
        Telemarketing::find($id)->update($request->all());

        return response()->json();
    }

    public function destroy($id)
    {
        $record = Telemarketing::find($id);
        $record->delete();

        return response()->json();
    }

    public function getList() {
        if(request()->ajax()) {
            return datatables()->of(User::where('branch_id', 2)->get())
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function filter(Request $request) 
    {
        \Log::info('Incoming request data:', $request->all());

        if(Auth::user()->hasRole('Super Admin') || Auth::user()->hasRole('SUPER ADMIN') || Auth::user()->hasRole('TELEMARKETING HEAD')) {
            $query = TelemarketingDetail::with('user', 'telemarketing', 'telemarketing.company', 'csd', 'csd.item') ->join('sale_details', 'telemarketing_details.order_id', '=', 'sale_details.id')
                ->join('sales', 'sale_details.sale_id', '=', 'sales.id') 
                ->whereHas('csd.item', function ($query) {
                    $query->where('branch_id', 2);
                })
                ->where('telemarketing_details.assigned_to', 1)
                ->where('sales.branch_id', '!=', 3)
                ->groupBy('sales.company_id')
                ->select('telemarketing_details.*') 
               ->when($request->accessories != 1, function ($query) {
                $query->whereHas('csd.item', function ($query) {
                    $query->where('branch_id', 2);
                });
            })
            ->orderBy('telemarketing_details.id', 'desc');
        } else {
            $query = TelemarketingDetail::with('user', 'telemarketing', 'telemarketing.company', 'csd', 'csd.item')
            ->when($request->accessories != 1, function ($query) {
                $query->whereHas('csd.item', function ($query) {
                    $query->where('branch_id', 2);
                });
            })
            ->where('assigned_to', Auth::user()->id)
            ->orderBy('telemarketing_details.id', 'desc');
        }

        if ($request->has('company') && !empty($request->company)) {
            \Log::info('Company filter applied: ', [$request->company]);
            $query->whereHas('telemarketing', function($q) use($request) {
                $q->where('company_id', $request->company);
            });
        }
        if ($request->has('assigned_to') && !empty($request->assigned_to)) {
            \Log::info('Assigned to filter applied: ', [$request->assigned_to]);
            $query->where('assigned_to', $request->assigned_to);
        }
        if ($request->has('status') && !empty($request->status)) {
            \Log::info('Status filter applied: ', [$request->status]);
            $query->where('status', $request->status);
        }
        if ($request->has('start_date') && !empty($request->start_date) && $request->has('end_date') && !empty($request->end_date)) {
            \Log::info('Date filter applied: ', [$request->start_date, $request->end_date]);
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        }
        if ($request->has('p_start_date') && !empty($request->p_start_date) && $request->has('p_end_date') && !empty($request->p_end_date)) {
            \Log::info('Date filter applied: ', [$request->p_start_date, $request->p_end_date]);
            $query->whereHas('csd.sale', function($q) use($request) {
                $q->whereBetween('date_purchased', [$request->p_start_date, $request->p_end_date]);
            });
        }
        if ($request->unassigned != 0) {
            \Log::info('Unassigned filter applied');
            $query->where('assigned_to', 1);
        }
        if ($request->contact != 0) {
            \Log::info('Contact filter applied: ', [$request->contact]);
            $query->whereHas('telemarketing.company', function($q) use($request) {
                $q->where('contact_no', $request->contact);
            });
        }
        
        if ($request->calls != 0) {
            \Log::info('Calls filter applied');
            $query->where('date', '>', date('Y-m-d'));
        }

        if ($request->ajax()) {
            return DataTables::eloquent($query->groupBy('telemarketing_id'))
                ->addIndexColumn() 
                ->make(true);
        }


        return response()->json(['error' => 'Invalid request'], 400);
    }


}

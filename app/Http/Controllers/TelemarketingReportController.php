<?php

namespace App\Http\Controllers;

use App\Company;
use App\TelemarketingDetail;
use App\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class TelemarketingReportController extends Controller
{
    public function index()
    {
        $companies = Company::where('active', 1)->orderBy('company_name')->get();
        $telemarketers = User::where('branch_id', 2)->orWhere('id', 1)->orderBy('name')->get();

        return view('backend.pages.reports.telemarketing_report', compact('companies', 'telemarketers'));
    }

    public function data(Request $request)
    {
        $query = $this->buildQuery($request)
            ->with([
                'user',
                'telemarketing.company',
                'csd.sale',
            ])
            ->orderByDesc('id');

        return DataTables::eloquent($query)->addIndexColumn()->toJson();
    }

    public function summary(Request $request)
    {
        $query = $this->buildQuery($request);

        return response()->json([
            'total_transactions' => (clone $query)->count(),
            'total_sales_amount' => (float) (clone $query)->sum('total_amount'),
        ]);
    }

    private function buildQuery(Request $request)
    {
        $query = TelemarketingDetail::query();

        if ($request->filled('company_id')) {
            $companyId = $request->company_id;
            $query->whereHas('telemarketing', function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            });
        }

        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $this->applyRange($query, 'date', $request->follow_up_start, $request->follow_up_end);

        if ($request->filled('date_purchased_start') || $request->filled('date_purchased_end')) {
            $start = $request->date_purchased_start;
            $end = $request->date_purchased_end;
            $query->whereHas('csd.sale', function ($q) use ($start, $end) {
                if (!empty($start) && !empty($end)) {
                    $q->whereBetween('date_purchased', [$start, $end]);
                    return;
                }
                if (!empty($start)) {
                    $q->whereDate('date_purchased', '>=', $start);
                }
                if (!empty($end)) {
                    $q->whereDate('date_purchased', '<=', $end);
                }
            });
        }

        return $query;
    }

    private function applyRange($query, $column, $start, $end)
    {
        if (!empty($start) && !empty($end)) {
            $query->whereBetween($column, [$start, $end]);
            return;
        }

        if (!empty($start)) {
            $query->whereDate($column, '>=', $start);
        }

        if (!empty($end)) {
            $query->whereDate($column, '<=', $end);
        }
    }
}


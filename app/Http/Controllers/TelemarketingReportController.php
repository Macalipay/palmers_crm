<?php

namespace App\Http\Controllers;

use App\Company;
use App\TelemarketingDetail;
use App\User;
use App\Exports\TelemarketingReportExport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelWriter;
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

    public function export(Request $request)
    {
        $format = strtolower((string) $request->get('format', 'xlsx'));
        if (!in_array($format, ['xlsx', 'csv', 'json'])) {
            $format = 'xlsx';
        }

        $query = $this->buildQuery($request)
            ->with([
                'user',
                'telemarketing.company',
                'csd.sale',
            ])
            ->orderByDesc('id');

        if ($format === 'json') {
            $total = (clone $query)->count();
            $maxJsonRows = 5000;
            if ($total > $maxJsonRows) {
                return response()->json([
                    'message' => 'Too many records for browser export. Please use Excel or CSV export.',
                ], 422);
            }

            return response()->json($this->buildExportData((clone $query)->get()));
        }

        $filename = 'telemarketing_report_' . date('Ymd_His') . '.' . $format;
        $writerType = $format === 'csv' ? ExcelWriter::CSV : ExcelWriter::XLSX;

        return Excel::download(new TelemarketingReportExport($query), $filename, $writerType);
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

    private function buildExportData($records)
    {
        $headings = [
            '#',
            'Company',
            'PO/FO No',
            'Follow Up Date',
            'Status',
            'Assigned To',
            'Contact Person',
            'Contact No',
            'Date Purchased',
            'Total Amount',
            'Remarks',
        ];

        $rows = [];
        $index = 1;
        foreach ($records as $record) {
            $rows[] = [
                $index++,
                optional(optional($record->telemarketing)->company)->company_name ?: '--',
                optional(optional($record->csd)->sale)->po_no ?: '--',
                $this->formatDate($record->date),
                $record->status ?: '--',
                optional($record->user)->name ?: 'UNASSIGNED',
                optional(optional($record->telemarketing)->company)->contact_person ?: '--',
                optional(optional($record->telemarketing)->company)->contact_no ?: '--',
                $this->formatDate(optional(optional($record->csd)->sale)->date_purchased),
                is_null($record->total_amount) ? 0 : (float) $record->total_amount,
                $record->remarks ?: '--',
            ];
        }

        return [
            'headings' => $headings,
            'rows' => $rows,
        ];
    }

    private function formatDate($value)
    {
        if (empty($value)) {
            return '--';
        }

        try {
            return Carbon::parse($value)->format('M d, Y');
        } catch (\Exception $e) {
            return '--';
        }
    }
}


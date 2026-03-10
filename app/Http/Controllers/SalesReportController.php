<?php

namespace App\Http\Controllers;

use App\Exports\SalesReportExport;
use App\Branch;
use App\Company;
use App\Division;
use App\Merchandiser;
use App\Sale;
use App\SalesAssociate;
use App\Source;
use App\Store;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelWriter;
use Yajra\DataTables\Facades\DataTables;

class SalesReportController extends Controller
{
    public function index()
    {
        $sources = Source::where('active', 1)->orderBy('source')->get();
        $salesAgents = User::where('designation', 'SALES AGENT')->orderBy('name')->get();
        $salesAssociates = SalesAssociate::where('active', 1)->orderBy('sales_associate')->get();
        $merchandisers = Merchandiser::orderBy('merchandiser')->get();
        $divisions = Division::where('active', 1)->orderBy('division')->get();
        $branches = Branch::where('active', 1)->orderBy('branch_name')->get();
        $companies = Company::where('active', 1)->orderBy('company_name')->get();
        $stores = Store::orderBy('store_name')->get();

        return view('backend.pages.reports.sales_report', compact(
            'sources',
            'salesAgents',
            'salesAssociates',
            'merchandisers',
            'divisions',
            'branches',
            'companies',
            'stores'
        ));
    }

    public function data(Request $request)
    {
        $query = $this->buildQuery($request)->with([
            'company',
            'company.province',
            'store',
            'source',
            'user',
            'sales_associate',
            'merchandiser',
            'division',
            'branch',
        ])->orderByDesc('id');

        $dataTable = DataTables::eloquent($query)->addIndexColumn();

        if ((int) $request->get('export_all', 0) === 1) {
            $dataTable->skipPaging();
        }

        return $dataTable->toJson();
    }

    public function summary(Request $request)
    {
        $query = $this->buildQuery($request);
        $totalTransactions = (clone $query)->count();
        $totalSalesAmount = (clone $query)->sum('amount');

        return response()->json([
            'total_transactions' => $totalTransactions,
            'total_sales_amount' => (float) $totalSalesAmount,
        ]);
    }

    public function export(Request $request)
    {
        $format = strtolower((string) $request->get('format', 'xlsx'));
        if (!in_array($format, ['xlsx', 'csv', 'json'])) {
            $format = 'xlsx';
        }

        $query = $this->buildQuery($request)->with([
            'company',
            'store',
            'source',
            'user',
            'sales_associate',
            'merchandiser',
            'division',
            'branch',
        ])->orderByDesc('id');

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

        $filename = 'sales_report_' . date('Ymd_His') . '.' . $format;
        $writerType = $format === 'csv' ? ExcelWriter::CSV : ExcelWriter::XLSX;

        return Excel::download(new SalesReportExport($query), $filename, $writerType);
    }

    private function buildExportData($sales)
    {
        $headings = [
            '#',
            'Company',
            'Store',
            'Industry',
            'Source',
            'Payment Term',
            'PO/OF No',
            'Date Purchased',
            'Amount',
            'Sales Agent',
            'Sales Associate',
            'Merchandiser',
            'Division',
            'Branch',
            'Agreed Delivery',
            'Actual Delivery',
            'Date Posted',
            'Date Encode',
            'Date Received',
            'Date Filed',
            'Deadline',
            'Project Title',
            'Contact Person',
            'Telephone',
            'Email',
            'Status',
        ];

        $rows = [];
        $index = 1;
        foreach ($sales as $sale) {
            $rows[] = [
                $index++,
                optional($sale->company)->company_name ?: '--',
                optional($sale->store)->store_name ?: '--',
                optional($sale->company)->industry ?: '--',
                optional($sale->source)->source ?: '--',
                $sale->payment_term ?: '--',
                $sale->po_no ?: '--',
                $this->formatDate($sale->date_purchased),
                is_null($sale->amount) ? 0 : (float) $sale->amount,
                optional($sale->user)->name ?: '--',
                optional($sale->sales_associate)->sales_associate ?: '--',
                optional($sale->merchandiser)->merchandiser ?: '--',
                optional($sale->division)->division ?: '--',
                optional($sale->branch)->branch_name ?: '--',
                $this->formatDate($sale->agreed_delivery_date),
                $this->formatDate($sale->actual_delivery_date),
                $this->formatDate($sale->date_posted),
                $this->formatDate($sale->date_encode),
                $this->formatDate($sale->date_received),
                $this->formatDate($sale->date_filed),
                $this->formatDate($sale->deadline),
                $sale->project_title ?: '--',
                $sale->contact_person ?: '--',
                $sale->telephone_no ?: '--',
                $sale->email ?: '--',
                ((string) $sale->active === '1') ? 'ACTIVE' : 'INACTIVE',
            ];
        }

        return [
            'headings' => $headings,
            'rows' => $rows,
        ];
    }

    private function buildQuery(Request $request)
    {
        $query = Sale::query();

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }
        if ($request->filled('store_id')) {
            $query->where('store_id', $request->store_id);
        }
        if ($request->filled('source_id')) {
            $query->where('source_id', $request->source_id);
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('sales_associate_id')) {
            $query->where('sales_associate_id', $request->sales_associate_id);
        }
        if ($request->filled('merchandiser_id')) {
            $query->where('merchandiser_id', $request->merchandiser_id);
        }
        if ($request->filled('division_id')) {
            $query->where('division_id', $request->division_id);
        }
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        $this->applyRange($query, 'date_purchased', $request->date_purchased_start, $request->date_purchased_end);

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

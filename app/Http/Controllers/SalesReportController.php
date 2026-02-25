<?php

namespace App\Http\Controllers;

use App\Branch;
use App\Company;
use App\Division;
use App\Merchandiser;
use App\Sale;
use App\SalesAssociate;
use App\Source;
use App\Store;
use App\User;
use Illuminate\Http\Request;
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
}

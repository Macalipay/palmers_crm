<?php

namespace App\Http\Controllers;

use App\TelemarketingDetailReport;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class TelemarketingDetailReportController extends Controller
{
    public function index()
    {
        return view('backend.pages.reports.telemarketing_item_reports');
    }

    public function get(Request $request)
    {
        if (!$request->ajax()) {
            return response()->json(['error' => 'Invalid request'], 400);
        }

        $query = TelemarketingDetailReport::with([
            'reporter',
            'telemarketingDetail.telemarketing.company',
            'telemarketingDetail.csd.sale',
        ])->orderBy('id', 'desc');

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->make(true);
    }
}

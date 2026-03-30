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
            'resolver',
            'telemarketingDetail.telemarketing.company',
            'telemarketingDetail.csd.sale',
        ])->orderBy('id', 'desc');

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->make(true);
    }

    public function resolve(Request $request, $id)
    {
        $validated = $request->validate([
            'resolution_remarks' => ['nullable', 'string', 'max:2000'],
        ]);

        $report = TelemarketingDetailReport::findOrFail($id);

        $report->update([
            'status' => 'RESOLVED',
            'resolution_remarks' => $validated['resolution_remarks'] ?? null,
            'resolved_by' => auth()->id(),
            'resolved_at' => now(),
            'updated_by' => auth()->id(),
        ]);

        return response()->json([
            'message' => 'Report marked as resolved.',
        ]);
    }
}

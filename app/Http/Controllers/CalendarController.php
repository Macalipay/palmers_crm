<?php

namespace App\Http\Controllers;

use App\Calendar;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function index()
    {
        $total_event = Calendar::count();
        $todo = Calendar::where('status', 'TO DO')->count();
        $inprogress = Calendar::where('status', 'IN PROGRESS')->count();
        $cancelled = Calendar::where('status', 'CANCELLED')->count();
        $pending = Calendar::where('status', 'PENDING')->count();
        $onhold = Calendar::where('status', 'ONHOLD')->count();
        $completed = Calendar::where('status', 'COMPLETED')->count();
        return view('backend.pages.calendar.calendar', compact('total_event', 'todo', 'inprogress', 'cancelled', 'pending', 'onhold', 'completed'));
    }

    public function get(Request $request)
    {
        if($request->ajax()) {
            $data = Calendar::get(['id', 'title', 'start', 'end', 'color', 'status', 'location']);

            return response()->json($data);
       }
    }

    public function status_filter(Request $request, $status)
    {
        if($request->ajax()) {
            $data = Calendar::where('status', $status)->get(['id', 'title', 'start', 'end', 'color', 'status']);

            return response()->json($data);
       }
    }
}

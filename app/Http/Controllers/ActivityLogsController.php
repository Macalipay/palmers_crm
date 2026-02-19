<?php

namespace App\Http\Controllers;

use Auth;
use App\ActivityLogs;
use Illuminate\Http\Request;
use Jenssegers\Agent\Facades\Agent;


class ActivityLogsController extends Controller
{
    public function index() {
        return view('backend.pages.maintenance.activity_logs');
    }
    
    public function get() {
        if(request()->ajax()) {
            return datatables()->of(ActivityLogs::with('user')->orderBy('id', 'desc')->limit(500)->get())
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function save($activity, $details, $ip) {
        $data = array(
            'user_id' => Auth::user()->id,
            'activity_type' => $activity,
            'details' => $details,
            "ip_address" => $ip,
            "device_info" => Agent::browser()
        );

        ActivityLogs::create($data);
    }
}

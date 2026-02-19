<?php

namespace App\Http\Controllers;

use App\Telemarketing;
use App\Company;
use App\User;
use App\Source;
use Illuminate\Http\Request;
use Auth;
use App\ActivityLogs;


class RetentionController extends Controller
{
    public function index() {
        $sources = Source::where('active', 1)->get();
        $companies = Company::where('active', 1)->get();
        $users = User::where('designation', 'SALES AGENT ')->get();
        return view('backend.pages.telemarketing.retention', compact('sources', 'users', 'companies'));
    }

    public function get() {
        if(request()->ajax()) {
            return datatables()->of(Telemarketing::with('company', 'company.province', 'source')->where('lead_status', 'RETENTION')->get())
            ->addIndexColumn()
            ->make(true);
        }
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

        $user = Telemarketing::create($request->all());

        $result = (new ActivityLogsController)->save('create', 'Created a telemarketing record with Retention ID: '.$user->id, $request->ip());

        return response()->json(compact('validate'));
    }

    public function edit($id)
    {
        $data = Telemarketing::where('id', $id)->orderBy('id')->firstOrFail();

        $result = (new ActivityLogsController)->save('view', 'Viewed a telemarketing record with Retention ID: '.$id, request()->ip());

        return response()->json(compact('data'));
    }


    public function update(Request $request, $id)
    {
        Telemarketing::find($id)->update($request->all());

        $result = (new ActivityLogsController)->save('update', 'Updated a telemarketing record with Retention ID: '.$id, request()->ip());

        return response()->json();
    }

    public function destroy($id)
    {
        $record = Telemarketing::find($id);
        $record->delete();

        $result = (new ActivityLogsController)->save('delete', 'Deleted a telemarketing record with Retention ID: '.$id, request()->ip());

        return response()->json();
    }

    public function generate_retention()
    {
        $retentions = Company::get();

        foreach ($retentions as $retention) {
            $retention_record = Telemarketing::where('company_id', $retention->id)->first();

            if (!$retention_record) {
                $regular = [
                    'company_id' => $retention->id,
                    'lead_status' => 'WON',
                    'opportunity_status' => 'DEAL',
                    'source_id' => 1,
                    'product_interest' => 'FIRE EQUIPMENT',
                    'active' => 'ACTIVE',
                    'created_by' => Auth::user()->id,
                    'updated_by' => Auth::user()->id,
                ];
                Telemarketing::create($regular);
            }
        }
    }
}

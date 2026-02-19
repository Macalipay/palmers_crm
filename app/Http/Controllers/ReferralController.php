<?php

namespace App\Http\Controllers;

use App\Referral;
use Illuminate\Http\Request;
use Auth;

class ReferralController extends Controller
{
    public function index()
    {
        $referrals = Referral::get();
        return view('backend.pages.referral.referral', compact('referrals'));

    }

    public function get() {
        if(request()->ajax()) {
            return datatables()->of(Referral::with('referral')->where('placement_id', '!=', 0)->get())
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function save(Request $request) {
        $validate = $request->validate([
            'name' => ['required', 'max:250'],
            'placement_id' => ['required', 'max:250'],
            'node_address' => ['nullable', 'max:250'],
            'contact_no' => ['nullable', 'max:250'],
            'company_name' => ['nullable', 'max:250'],
            'location' => ['nullable', 'max:250'],
            'location_id' => ['nullable', 'max:250'],
            'status' => ['nullable', 'max:250'],
            'text' => ['nullable', 'max:250'],
            'outcome' => ['nullable', 'max:250'],
        ]);

        $request['created_by'] = Auth::user()->id;
        $request['updated_by'] = Auth::user()->id;

        $user = Referral::create($request->all());

        $result = (new ActivityLogsController)->save('create', 'Created a company record with Referral Name: '.$user->company_name, $request->ip());

        return response()->json(compact('validate'));
    }

    public function edit($id)
    {
        $data = Referral::where('id', $id)->orderBy('id')->firstOrFail();

        $result = (new ActivityLogsController)->save('view', 'Viewed the record with Referral Name: '.$data->company_name, request()->ip());
        return response()->json(compact('data'));
    }


    public function update(Request $request, $id)
{
    $company = Referral::findOrFail($id);

    // Store the old data
    $oldData = $company->toArray();

    // Update the company with the new data
    $company->update($request->all());

    // Store the new data
    $newData = $company->toArray();

    // Exclude 'updated_at' from the changes if it has been updated
    if (isset($oldData['updated_at']) && isset($newData['updated_at']) && $oldData['updated_at'] != $newData['updated_at']) {
        unset($oldData['updated_at']);
        unset($newData['updated_at']);
    }

    // Compare old and new data to find changes
    $changes = array_diff_assoc($newData, $oldData);

    // Construct the log message
    $logMessage = 'Updated the record with Referral Name: '.$company->company_name.'. Changes: ';
    foreach ($changes as $key => $value) {
        $logMessage .= ucfirst($key) . ' from ' . $oldData[$key] . ' to ' . $value . '; ';
    }

    // Save the activity log
    $result = (new ActivityLogsController)->save('update', $logMessage, request()->ip());

    return response()->json();
}

    public function destroy($id)
    {
        $record = Referral::find($id);
        $record->delete();

        $result = (new ActivityLogsController)->save('delete', 'Deleted the record with Referral Name: '.$record->company_name, request()->ip());

        return response()->json();
    }

    public function referral_tree()
    {
        return view('backend.pages.referral.referral_tree');
    }

    public function parseJSON($id)
    {
        $member = Referral::where('id', $id)->firstOrFail();

        $childrens = [];

        $members = $this->getMembers($member->id);
        foreach ($members as $mb) {
            array_push($childrens, $mb);
            $subChild = Referral::where('placement_id', $mb->id)->get();
              if($subChild->count() > 0) {
                $mb['children'] = $subChild;
                foreach ($subChild as $sc) {
                    $child = Referral::where('placement_id', $sc->id)->get();
                    if($subChild->count() > 0) {
                        $sc['children'] = $child;
                        foreach($child as $c) {
                            $child_1 = Referral::where('placement_id', $c->id)->get();
                            if($subChild->count() > 0) {
                                $c['children'] = $child_1;
                                foreach($child_1 as $c_1) {
                                    $child_2 = Referral::where('placement_id', $c_1->id)->get();
                                    if($child_1->count() > 0) {
                                        $c_1['children'] = $child_2;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $member['children'] = $childrens;

        return $member;
    }

    public function getMembers($id) {
        return Referral::where('placement_id', $id)->get();
    }

    public function parent() {
        $parent = Referral::where('id', 2)->get();

        return response()->json(compact('parent'));
    }
}

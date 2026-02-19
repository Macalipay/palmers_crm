<?php

namespace App\Http\Controllers;

use App\Branch;
use Illuminate\Http\Request;
use Auth;

class BranchController extends Controller
{
    public function index() {
        return view('backend.pages.maintenance.branch');
    }

    public function get($id) {
        if(request()->ajax()) {
            return datatables()->of(Branch::where('division_id', $id)->get())
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function save(Request $request) {
        $validate = $request->validate([
            'branch_name' => ['required', 'max:250'],
            'active' => ['required', 'max:250'],
        ]);

        $request['created_by'] = Auth::user()->id;
        $request['updated_by'] = Auth::user()->id;

        Branch::create($request->all());

        return response()->json(compact('validate'));
    }

    public function edit($id)
    {
        $data = Branch::where('id', $id)->orderBy('id')->firstOrFail();
        return response()->json(compact('data'));
    }


    public function update(Request $request, $id)
    {
        Branch::find($id)->update($request->all());

        return response()->json();
    }

    public function destroy($id)
    {
        $record = Branch::find($id);
        $record->delete();

        return response()->json();
    }
    
    public function get_list($id)
    {
        $data = Branch::where('division_id', $id)->orderBy('id')->get();
        return response()->json(compact('data'));
    }
}

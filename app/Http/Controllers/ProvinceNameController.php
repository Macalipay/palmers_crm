<?php

namespace App\Http\Controllers;

use App\ProvinceName;
use Illuminate\Http\Request;
use Auth;

class ProvinceNameController extends Controller
{
    public function index() {
        return view('backend.pages.maintenance.province');
    }

    public function get() {
        if(request()->ajax()) {
            return datatables()->of(ProvinceName::get())
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function save(Request $request) {
        $validate = $request->validate([
            'province' => ['required', 'max:250'],
            'active' => ['required', 'max:250'],
        ]);

        $request['created_by'] = Auth::user()->id;
        $request['updated_by'] = Auth::user()->id;

        ProvinceName::create($request->all());

        return response()->json(compact('validate'));
    }

    public function edit($id)
    {
        $data = ProvinceName::where('id', $id)->orderBy('id')->firstOrFail();
        return response()->json(compact('data'));
    }


    public function update(Request $request, $id)
    {
        ProvinceName::find($id)->update($request->all());

        return response()->json();
    }

    public function destroy($id)
    {
        $record = ProvinceName::find($id);
        $record->delete();

        return response()->json();
    }
}

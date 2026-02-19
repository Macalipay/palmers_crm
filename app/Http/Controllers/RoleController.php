<?php

namespace App\Http\Controllers;

use App\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index() {
        return view('backend.pages.user_management.roles');
    }

    public function get() {
        if(request()->ajax()) {
            return datatables()->of(Role::get())
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function save(Request $request) {
        $validate = $request->validate([
            'name' => ['required', 'max:250'],
        ]);
        $request['guard_name'] = 'web';

        Role::create($request->all());

        return response()->json(compact('validate'));
    }

    public function edit($id)
    {
        $data = Role::where('id', $id)->orderBy('id')->firstOrFail();
        return response()->json(compact('data'));
    }

    
    public function update(Request $request, $id)
    {
        Role::find($id)->update($request->all());

        return response()->json();
    }

    public function destroy($id)
    {
        $record = Role::find($id);
        $record->delete();
        
        return response()->json();
    }
}

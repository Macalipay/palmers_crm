<?php

namespace App\Http\Controllers;

use App\SalesSerialNo;
use Illuminate\Http\Request;

class SalesSerialNoController extends Controller
{
    public function get($id) {
        if(request()->ajax()) {
            return datatables()->of(SalesSerialNo::where('sale_details_id', $id)->get())
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function save(Request $request) {
        $check = SalesSerialNo::where('sale_details_id', $request->sales_id)->where($request->type, $request->value)->count();
        if($check === 0) {
            SalesSerialNo::where('id', $request->id)->update([$request->type => ($request->value!==null?$request->value:'')]);
        }else {
            SalesSerialNo::where('id', $request->id)->update([$request->type => '']);
        }
    }
}

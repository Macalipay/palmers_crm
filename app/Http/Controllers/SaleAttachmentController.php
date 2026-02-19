<?php

namespace App\Http\Controllers;

use Auth;
use App\SaleAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class SaleAttachmentController extends Controller
{
    public function upload(Request $request) {

        $validation = $request->validate([
            'sale_id' => ['required']
        ]);

        if(isset($request->manual_attachment)) {
            foreach($request->manual_attachment as $file) {
                $filename = time().'_'.$file->getClientOriginalName();
                $filesize = $file->getSize();
                $extension = $file->extension();

                $file->storeAs('public/images/attachment/sale/', $filename);
                
                $data = array(
                    'sale_id'=> $request->sale_id,
                    'filename'=>$filename,
                );

                SaleAttachment::create($data);
            }
        }

        return response()->json();
    }
    
    public function view_attachment(Request $request) {
        $attachment = SaleAttachment::where('sale_id', $request->id)->get();
        return response()->json(compact('attachment'));
    }

    public function deleteFile($id) {
        $file = SaleAttachment::find($id);
        $file->delete();
    }
}

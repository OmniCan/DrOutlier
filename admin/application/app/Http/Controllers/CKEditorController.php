<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CKEditorController extends Controller
{
    public function upload(Request $request)
    {
        if($request->hasFile('upload')) { 
            $fileName = fileUploader($request->upload,getFilePath('CKImage'));   
            $CKEditorFuncNum = $request->input('CKEditorFuncNum');
            $url = getImage(getFilePath('CKImage').'/'.$fileName);
            
            $msg = 'Image successfully uploaded'; 
            $response = "<script>window.parent.CKEDITOR.tools.callFunction($CKEditorFuncNum, '$url', '$msg')</script>";
               
            @header('Content-type: text/html; charset=utf-8'); 
            echo $response;
        }
    }
}
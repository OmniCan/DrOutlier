<?php

namespace App\Http\Controllers\Api;
 
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Faq; 
use App\Http\Controllers\Controller; 

class FaqController extends Controller
{
    public function index(){
        $pageTitle = 'Help Center';
        
        $datalist = Faq::all(); 

        $notify[] = 'Help List Success !!';
        return response()->json([ 
            'status'=>'success',
            'message'=>['success'=>$notify],
            'data'=>[ 
                'helplist'=>$datalist,
            ]
        ]);
    }

 
}

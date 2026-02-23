<?php

namespace App\Http\Controllers\Api;
 
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; 
use App\Models\Basic;
use App\Http\Controllers\Controller; 

class BasicsController extends Controller
{
    public function index(Request $request){
        $pageTitle = 'Basic List';
 
         
        $data_query = Basic::with('categories')->orderBy('id' , 'DESC');
        if ($request->category == 1) { 
            $data_query = $data_query->where("category", $request->category); 
        }elseif($request->category == 2) { 
            $data_query = $data_query->where("category", $request->category); 
        }else{
            $data_query = $data_query;
        }
        
        $basiclist = $data_query->paginate(20);

        $notify[] = 'Basic List Success !!';
        return response()->json([ 
            'status'=>'success',
            'message'=>['success'=>$notify],
            'data'=>[ 
                'basiclist'=>$basiclist,
            ]
        ]);
    }


    public function show(Request $request){ 

        $basicshow = Basic::find($request->id);
        
        

        $notify[] = 'Basic show Success !!';
        return response()->json([ 
            'status'=>'success',
            'message'=>['success'=>$notify],
            'data'=>[ 
                'basicshow'=>$basicshow,
            ]
        ]);
    }


    

   
     
}

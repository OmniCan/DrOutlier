<?php

namespace App\Http\Controllers\Api;
 
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; 
use App\Models\Munchie;
use App\Http\Controllers\Controller; 

class MunchiesController extends Controller
{
    public function index(Request $request){
        $pageTitle = 'Munchie List';
 
         
        $data_query = Munchie::with('categories')->orderBy('id' , 'DESC');
        if ($request->category == 1) { 
            $data_query = $data_query->where("category", $request->category); 
        }elseif($request->category == 2) { 
            $data_query = $data_query->where("category", $request->category); 
        }else{
            $data_query = $data_query;
        }
        
        $munchielist = $data_query->paginate(20);

        $notify[] = 'List Success !!';
        return response()->json([ 
            'status'=>'success',
            'message'=>['success'=>$notify],
            'data'=>[ 
                'munchielist'=>$munchielist,
            ]
        ]);
    }


    public function show(Request $request){ 

        $munchiehow = Munchie::find($request->id);
        
        $notify[] = 'Show Success !!';
        return response()->json([ 
            'status'=>'success',
            'message'=>['success'=>$notify],
            'data'=>[ 
                'munchiehow'=>$munchiehow,
            ]
        ]);
    }

    public function munchieDetails(Request $request){

        $datalist = Munchie::with('categories')->where('id' , $request->id)->first();

        return response()->json([ 
            'status'=>'success', 
            'data'=>[ 
                'datalist'=>$datalist,
            ]
        ]);
    }

   
     
}

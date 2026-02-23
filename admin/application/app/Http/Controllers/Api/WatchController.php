<?php

namespace App\Http\Controllers\Api;
 
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; 
use App\Models\WatchAndLearn;
use App\Http\Controllers\Controller; 

class WatchController extends Controller
{
    public function index(Request $request){
        $pageTitle = 'Watch and Learn List';
 
         
        $data_query = WatchAndLearn::with('categories')->orderBy('id' , 'DESC');
        if ($request->category == 1) { 
            $data_query = $data_query->where("category", $request->category); 
        }elseif($request->category == 2) { 
            $data_query = $data_query->where("category", $request->category); 
        }else{
            $data_query = $data_query;
        }
        
        $watchlist = $data_query->paginate(20);

        $notify[] = 'List Success !!';
        return response()->json([ 
            'status'=>'success',
            'message'=>['success'=>$notify],
            'data'=>[ 
                'watchlist'=>$watchlist,
            ]
        ]);
    }


    public function show(Request $request){ 

        $watchshow = WatchAndLearn::find($request->id);
        
        $notify[] = 'Show Success !!';
        return response()->json([ 
            'status'=>'success',
            'message'=>['success'=>$notify],
            'data'=>[ 
                'watchshow'=>$watchshow,
            ]
        ]);
    }

   
     
}

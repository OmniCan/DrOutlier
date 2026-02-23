<?php

namespace App\Http\Controllers\Api;
 
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\MemberOfParliament;
use App\Models\Blog;
use App\Http\Controllers\Controller; 

class BlogsController extends Controller
{
    public function index(Request $request){
        $pageTitle = 'Note List';
 
         
        $data_query = Blog::with('categories')->orderBy('sort_order' , 'ASC');
        if ($request->category == 1) { 
            $data_query = $data_query->where("category", $request->category); 
        }elseif($request->category == 2) { 
            $data_query = $data_query->where("category", $request->category); 
        }else{
            $data_query = $data_query;
        }
        
        $newslist = $data_query->paginate(20);

        $notify[] = 'Note List Success !!';
        return response()->json([ 
            'status'=>'success',
            'message'=>['success'=>$notify],
            'data'=>[ 
                'newslist'=>$newslist,
            ]
        ]);
    }


    public function show(Request $request){ 

        $newsshow = Blog::find($request->id);
        
        

        $notify[] = 'Note show Success !!';
        return response()->json([ 
            'status'=>'success',
            'message'=>['success'=>$notify],
            'data'=>[ 
                'newslist'=>$newsshow,
            ]
        ]);
    }

    public function noteDetails(Request $request){

        $datalist = Blog::with('categories')->where('id' , $request->id)->first();

        return response()->json([ 
            'status'=>'success', 
            'data'=>[ 
                'datalist'=>$datalist,
            ]
        ]);
    }

   
     
}

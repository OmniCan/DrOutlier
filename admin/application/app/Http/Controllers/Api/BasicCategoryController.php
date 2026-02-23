<?php

namespace App\Http\Controllers\Api;
 
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\BasicCategory; 
use App\Models\Basic; 
use App\Models\BasicBookmark;
use Auth;
use App\Http\Controllers\Controller; 
use App\Models\BasicReadStatus;
use Illuminate\Support\Facades\Validator;

class BasicCategoryController extends Controller
{
    public function index(){
        $pageTitle = 'Category';
        $user = Auth::user();
 
        $datalist = BasicCategory::where('parent_id', 0)->where('status' , 1)->paginate(20);
        
 
        $datalist = $datalist->map(function ($result) use ($user) { 
            $result->child = BasicCategory::withCount('notes')->where('parent_id', $result->id)->where('status', 1)->get()->map(function ($child) use ($user) { 
                    $readNoteRecord = BasicReadStatus::where('user_id', $user->id)->where('category', $child->id)->first();
                     
                    $child->read_note = $readNoteRecord ? $readNoteRecord->read_basic : 0;
                    
                    return $child;
                });
         
             
            return $result;
        });

         

        return response()->json([ 
            'status'=>'success', 
            'data'=>[ 
                'datalist'=>$datalist,
            ]
        ]);
    }


    public function Notes(Request $request){

        $notes = Basic::where('category' , $request->category)->paginate(20);

        return response()->json([ 
            'status'=>'success', 
            'data'=>[ 
                'notes'=>$notes,
            ]
        ]);

    }     


    public function changeNoteBookStatus(Request $request){

        $validator = Validator::make($request->all(), [
            'user_id'=>'required',
            'basic_id'=>'required',
        ]);

        $bookmark = BasicBookmark::where('user_id', $request->user_id)->where('basic_id', $request->basic_id)->first();

        if ($bookmark) { 
            $bookmark->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'UnSaved successfully !!',
            ]);
        } else { 
            $newBookmark = BasicBookmark::create([
                'user_id' => $request->user_id,
                'basic_id' => $request->basic_id,
            ]);
            return response()->json([
                'status' => 'success',
                'message' => 'Saved successfully !!',
                'data' => [
                    'list' => $newBookmark,
                ]
            ]);
        }
 
     }
 
     public function getBookBlogStatus(Request $request){
 
          
        $blogIds = BasicBookmark::where('user_id', $request->user_id)->pluck('basic_id');
        $spotters = Basic::whereIn('id', $blogIds)->paginate(10);

 
         return response()->json([
             'status' => 'success',
             'data' => [
                 'list' => $spotters,
             ]
         ]);
  
      }


    public function ReadStatus(Request $request){

        $user = Auth::user();
 
        
        $read = BasicReadStatus::updateOrCreate(
            [
                'user_id' => $user->id, 
                'category' => $request->category 
            ],
            [
                'read_basic' => $request->read_basic 
            ]
        );
        

        return response()->json([
             'status' => 'Added success !' 
         ]);
    }

 
}

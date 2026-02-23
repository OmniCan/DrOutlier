<?php

namespace App\Http\Controllers\Api;
 
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\WatchCategory; 
use App\Models\WatchAndLearn; 
use App\Models\WatchAndLearnBookmark;
use Auth;
use App\Http\Controllers\Controller; 
use App\Models\WatchAndLearnReadStatus;
use Illuminate\Support\Facades\Validator;

class WatchAndLearnCategoryController extends Controller
{
    public function index(){
        $pageTitle = 'Category';
        $user = Auth::user();
 
        $datalist = WatchCategory::where('parent_id', 0)->where('status' , 1)->paginate(20);
        
 
        $datalist = $datalist->map(function ($result) use ($user) { 
            $result->child = WatchCategory::withCount('notes')->where('parent_id', $result->id)->where('status', 1)->get()->map(function ($child) use ($user) { 
                    $readNoteRecord = WatchAndLearnReadStatus::where('user_id', $user->id)->where('category', $child->id)->first(); 
                    $child->read_note = $readNoteRecord ? $readNoteRecord->read_watch : 0;
                    
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

        $watchs = WatchAndLearn::where('category' , $request->category)->paginate(20);

        return response()->json([ 
            'status'=>'success', 
            'data'=>[ 
                'watchs'=>$watchs,
            ]
        ]);

    }     


    public function changeNoteBookStatus(Request $request){

        $validator = Validator::make($request->all(), [
            'user_id'=>'required',
            'watch_id'=>'required',
        ]);

        $bookmark = WatchAndLearnBookmark::where('user_id', $request->user_id)->where('watch_id', $request->watch_id)->first();

        if ($bookmark) { 
            $bookmark->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'UnSaved successfully !!',
            ]);
        } else { 
            $newBookmark = WatchAndLearnBookmark::create([
                'user_id' => $request->user_id,
                'watch_id' => $request->watch_id,
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
 
          
        $blogIds = WatchAndLearnBookmark::where('user_id', $request->user_id)->pluck('watch_id');
        $spotters = WatchAndLearn::whereIn('id', $blogIds)->paginate(10);

 
         return response()->json([
             'status' => 'success',
             'data' => [
                 'list' => $spotters,
             ]
         ]);
  
      }


    public function ReadStatus(Request $request){

        $user = Auth::user();
 
        
        $read = WatchAndLearnReadStatus::updateOrCreate(
            [
                'user_id' => $user->id, 
                'category' => $request->category 
            ],
            [
                'read_watch' => $request->read_watch 
            ]
        );
        

        return response()->json([
             'status' => 'Added success !' 
         ]);
    }

 
}

<?php

namespace App\Http\Controllers\Api;
 
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\MunchieCategory; 
use App\Models\Munchie; 
use App\Models\MunchieBookmark;
use Auth;
use App\Http\Controllers\Controller; 
use App\Models\MunchieReadStatus;
use Illuminate\Support\Facades\Validator;

class MunchiesCategoryController extends Controller
{
    public function index(){
        $pageTitle = 'Category';
        $user = Auth::user();
 
        $datalist = MunchieCategory::where('parent_id', 0)->where('status' , 1)->paginate(20);
        
 
        $datalist = $datalist->map(function ($result) use ($user) { 
            $result->child = MunchieCategory::withCount('notes')->where('parent_id', $result->id)->where('status', 1)->get()->map(function ($child) use ($user) { 
                    $readNoteRecord = MunchieReadStatus::where('user_id', $user->id)->where('category', $child->id)->first();
                     
                    $child->read_note = $readNoteRecord ? $readNoteRecord->read_munchie : 0;
                    
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

        $notes = Munchie::where('category' , $request->category)->paginate(20);

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
            'munchie_id'=>'required',
        ]);

        $bookmark = MunchieBookmark::where('user_id', $request->user_id)->where('munchie_id', $request->munchie_id)->first();

        if ($bookmark) { 
            $bookmark->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'UnSaved successfully !!',
            ]);
        } else { 
            $newBookmark = MunchieBookmark::create([
                'user_id' => $request->user_id,
                'munchie_id' => $request->munchie_id,
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
 
          
        $blogIds = MunchieBookmark::where('user_id', $request->user_id)->pluck('munchie_id');
        $spotters = Munchie::whereIn('id', $blogIds)->paginate(10);

 
         return response()->json([
             'status' => 'success',
             'data' => [
                 'list' => $spotters,
             ]
         ]);
  
      }


    public function ReadStatus(Request $request){

        $user = Auth::user();
 
        
        $read = MunchieReadStatus::updateOrCreate(
            [
                'user_id' => $user->id, 
                'category' => $request->category 
            ],
            [
                'read_munchie' => $request->read_munchie 
            ]
        );
        

        return response()->json([
             'status' => 'Added success !' 
         ]);
    }

 
}

<?php

namespace App\Http\Controllers\Api;
 
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Category; 
use App\Models\Blog; 
use App\Models\NoteBookmark;
use Auth;
use App\Http\Controllers\Controller; 
use App\Models\NoteReadStatus;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index(){
        $pageTitle = 'Category';
        $user = Auth::user();
 
        $datalist = Category::where('parent_id', 0)->where('status' , 1)->paginate(20);
 
        $datalist = $datalist->map(function ($result) use ($user) { 
            $result->child = Category::withCount('notes')->where('parent_id', $result->id)->where('status', 1)->get()->map(function ($child) use ($user) { 
                    $readNoteRecord = NoteReadStatus::where('user_id', $user->id)->where('category', $child->id)->first(); 
                    $child->read_note = $readNoteRecord ? $readNoteRecord->read_note : 0;
                    
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

        $notes = Blog::where('category' , $request->category)->paginate(20);

        return response()->json([ 
            'status'=>'success', 
            'data'=>[ 
                'notes'=>$notes,
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


    public function changeNoteBookStatus(Request $request){

        $validator = Validator::make($request->all(), [
            'user_id'=>'required',
            'blog_id'=>'required',
        ]);

        $bookmark = NoteBookmark::where('user_id', $request->user_id)->where('blog_id', $request->blog_id)->first();

        if ($bookmark) { 
            $bookmark->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'UnSaved successfully !!',
            ]);
        } else { 
            $newBookmark = NoteBookmark::create([
                'user_id' => $request->user_id,
                'blog_id' => $request->blog_id,
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
 
          
        $blogIds = NoteBookmark::where('user_id', $request->user_id)->pluck('blog_id');
        $spotters = Blog::whereIn('id', $blogIds)->paginate(10);

 
         return response()->json([
             'status' => 'success',
             'data' => [
                 'list' => $spotters,
             ]
         ]);
  
      }


    public function ReadStatus(Request $request){

        $user = Auth::user();
 
        
        $read = NoteReadStatus::updateOrCreate(
            [
                'user_id' => $user->id, 
                'category' => $request->category 
            ],
            [
                'read_note' => $request->read_note 
            ]
        );
        

        return response()->json([
             'status' => 'Added success !' 
         ]);
    }

 
}

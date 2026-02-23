<?php

namespace App\Http\Controllers\Admin;
 
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\MemberOfParliament;
use App\Models\WatchCategory; 
use App\Models\WatchAndLearn; 
use App\Http\Controllers\Controller; 
use App\Notifications\SendPushNotification;
use Illuminate\Notifications\Notification;
use Kutia\Larafirebase\Messages\FirebaseMessage;

class WatchController extends Controller
{

    public function via($notifiable)
    {
        return ['firebase'];
    }


    public function index(){
        $pageTitle = 'Watch and Learn List';
       $datalist  = $this->watchData();
        return view('admin.watch.index',compact('pageTitle','datalist'));
    }

    protected function watchData($scope = null)
    {

        $query = ($scope)
            ? WatchAndLearn::$scope()
            : WatchAndLearn::with(['categories']);

        $request = request();
        if ($request->search) {
            $search = $request->search;
            $query = $query->where('title', 'like', "%$search%");
        }
        $datalist  = $query->orderBy('sort_order', 'ASC')->paginate();

        return $datalist ;
    }
    public function create(){
        $pageTitle = 'Add Watch and Learn'; 

        $datalist = WatchCategory::where('parent_id', 0)->where('status' , 1)->get();

        $datalist = $datalist->map(function ($result) {
            $result->child = WatchCategory::where('parent_id', $result->id)->where('status' , 1)->get();
            return $result;
        });
        

        return view('admin.watch.create',compact('pageTitle','datalist'));
    }

    public function store(Request $request){
        
      
        $request->validate([
            'title' => 'required',
            'video_url' => 'required', 
        ]);

        $watch = new WatchAndLearn();
        $watch->category = $request->category;
        $watch->title = $request->title;
        $watch->sort_order = $request->sort_order;
        $watch->video_url = $request->video_url;  
        $watch->save();
 
        $notify[] = ['success', 'Added successfully'];
        
        return redirect()->route('admin.watch-and-learn.watch-index')->withNotify($notify);
    }


    public function edit($id){
        $pageTitle = 'Update Watch and Learn';
         
        $datalist = WatchCategory::where('parent_id', 0)->where('status' , 1)->get();

        $datalist = $datalist->map(function ($result) {
            $result->child = WatchCategory::where('parent_id', $result->id)->where('status' , 1)->get();
            return $result;
        });

        // $datalist = Category::where('status' , 1)->get();
        
        $watch = WatchAndLearn::find($id);
        return view('admin.watch.edit',compact('pageTitle','watch','datalist'));
    }


    public function update(Request $request,$id){

        $request->validate([
            'title' => 'required',
            'video_url' => 'required', 
        ]); 


        $watch = WatchAndLearn::findOrFail($id);
        $watch->category = $request->category;
        $watch->title = $request->title;
        $watch->sort_order = $request->sort_order;
        $watch->video_url = $request->video_url; 
        $watch->save();

        $notify[] = ['success', 'Updated successfully'];
        
        return redirect()->route('admin.watch-and-learn.watch-index')->withNotify($notify);
    }

    public function updateSortOrder(Request $request)
    {

        $request->validate([
            'sort_order' => 'required|integer',
        ]);

        try {
            $watch = WatchAndLearn::findOrFail($request->status_id);
            $watch->sort_order = $request->sort_order;
            $watch->save();

           
            return response()->json([
                'success' => true,
                'message' => 'Sort order updated successfully.',
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Failed to update sort order. Please try again.',
            ]);
        }
    }

    public function delete($id){
  
        $watch = WatchAndLearn::findOrFail($id); 
        $watch->delete();



        $notify[] = ['success', 'Deleted successfully'];
        return back()->withNotify($notify);
    }

    public function updateToken(Request $request){
        try{
            $request->user()->update(['fcm_token'=>$request->token]);
            return response()->json([
                'success'=>true
            ]);
        }catch(\Exception $e){
            report($e);
            return response()->json([
                'success'=>false
            ],500);
        }
    }
}

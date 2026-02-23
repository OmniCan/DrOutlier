<?php

namespace App\Http\Controllers\Admin;
 
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\MemberOfParliament;
use App\Models\BasicCategory; 
use App\Models\Basic; 
use App\Http\Controllers\Controller; 
use App\Notifications\SendPushNotification;
use Illuminate\Notifications\Notification;
use Kutia\Larafirebase\Messages\FirebaseMessage;

class BasicsController extends Controller
{

    public function via($notifiable)
    {
        return ['firebase'];
    }
    public function index(){
        $pageTitle = 'Basic List';
        $basiclist = $this->basicData();
        return view('admin.basic.index',compact('pageTitle','basiclist'));
    }
    protected function basicData($scope = null)
    {
        $query = ($scope)
            ? Basic::$scope()
            : Basic::with(['categories']);

        $request = request();
        if ($request->search) {
            $search = $request->search;
            $query = $query->where('title', 'like', "%$search%");
        }
        $basiclist = $query->orderBy('sort_order', 'ASC')->paginate();

        return $basiclist;
    }

    public function create(){
        $pageTitle = 'Add Basic'; 

        $datalist = BasicCategory::where('parent_id', 0)->where('status' , 1)->get();

        $datalist = $datalist->map(function ($result) {
            $result->child = BasicCategory::where('parent_id', $result->id)->where('status' , 1)->get();
            return $result;
        });
        

        return view('admin.basic.create',compact('pageTitle','datalist'));
    }

    public function store(Request $request){
        
      
        $request->validate([
            'title' => 'required',
            'content' => 'required', 
        ]);

        $basic = new Basic();
        $basic->category = $request->category;
        $basic->title = $request->title;
        $basic->sort_order = $request->sort_order;
        $basic->content = $request->content;  
        $basic->save();
 
        $notify[] = ['success', 'Added successfully'];
        
        return redirect()->route('admin.basic.basic-index')->withNotify($notify);
    }


    public function edit($id){
        $pageTitle = 'Update Basic';
         
        $datalist = BasicCategory::where('parent_id', 0)->where('status' , 1)->get();

        $datalist = $datalist->map(function ($result) {
            $result->child = BasicCategory::where('parent_id', $result->id)->where('status' , 1)->get();
            return $result;
        });

        // $datalist = Category::where('status' , 1)->get();
        
        $basic = Basic::find($id);
        return view('admin.basic.edit',compact('pageTitle','basic','datalist'));
    }


    public function update(Request $request,$id){

        $request->validate([
            'title' => 'required',
            'content' => 'required', 
        ]); 


        $basic = Basic::findOrFail($id);
        $basic->category = $request->category;
        $basic->title = $request->title;
        $basic->sort_order = $request->sort_order;
        $basic->content = $request->content; 
        $basic->save();

        $notify[] = ['success', 'Updated successfully'];
        
        return redirect()->route('admin.basic.basic-index')->withNotify($notify);
    }

    public function updateSortOrder(Request $request)
    {

        $request->validate([
            'sort_order' => 'required|integer',
        ]);

        try {
            $basics = Basic::findOrFail($request->status_id);
            $basics->sort_order = $request->sort_order;
            $basics->save();

           
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
  
        $basic = Basic::findOrFail($id); 
        $basic->delete();



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

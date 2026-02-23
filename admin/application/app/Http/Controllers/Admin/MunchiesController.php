<?php

namespace App\Http\Controllers\Admin;
 
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\MemberOfParliament;
use App\Models\MunchieCategory; 
use App\Http\Controllers\Controller;
use App\Models\Munchie;
use Illuminate\Supports\Facades\DB;
use App\Notifications\SendPushNotification;
use Illuminate\Notifications\Notification;
use Kutia\Larafirebase\Messages\FirebaseMessage;

class MunchiesController extends Controller
{

    public function via($notifiable)
    {
        return ['firebase'];
    }


    public function index(){
        $pageTitle = 'Munchie List';
         $munchielist  = $this->munchiesData();
        return view('admin.munchies.index',compact('pageTitle','munchielist'));
    }

    protected function munchiesData($scope = null)
    {

        $query = ($scope)
            ? Munchie::$scope()
            : Munchie::with(['categories']);

        $request = request();
        if ($request->search) {
            $search = $request->search;
            $query = $query->where('title', 'like', "%$search%");
        }
        $munchielist = $query->orderBy('sort_order', 'ASC')->paginate();

        return $munchielist;
    }
    public function create(){
        $pageTitle = 'Add Munchie'; 

        $datalist = MunchieCategory::where('parent_id', 0)->where('status' , 1)->get();

        $datalist = $datalist->map(function ($result) {
            $result->child = MunchieCategory::where('parent_id', $result->id)->where('status' , 1)->get();
            return $result;
        });
        

        return view('admin.munchies.create',compact('pageTitle','datalist'));
    }

    public function store(Request $request){
        
      
        $request->validate([
            'title' => 'required',
            'content' => 'required', 
        ]);

        $munchie = new Munchie();
        $munchie->category = $request->category;
        $munchie->title = $request->title;
        $munchie->sort_order = $request->sort_order;
        $munchie->content = $request->content;  
        $munchie->save();
 
        $notify[] = ['success', 'Added successfully'];
        
        return redirect()->route('admin.munchies.munchies-index')->withNotify($notify);
    }


    public function edit($id){
        $pageTitle = 'Update Munchie';
         
        $datalist = MunchieCategory::where('parent_id', 0)->where('status' , 1)->get();

        $datalist = $datalist->map(function ($result) {
            $result->child = MunchieCategory::where('parent_id', $result->id)->where('status' , 1)->get();
            return $result;
        });

        // $datalist = Category::where('status' , 1)->get();
        
        $munchie = Munchie::find($id);
        return view('admin.munchies.edit',compact('pageTitle','munchie','datalist'));
    }


    public function update(Request $request,$id){

        $request->validate([
            'title' => 'required',
            'content' => 'required', 
        ]); 


        $munchie = Munchie::findOrFail($id);
        $munchie->category = $request->category;
        $munchie->title = $request->title;
        $munchie->sort_order = $request->sort_order;
        $munchie->content = $request->content; 
        $munchie->save();

        $notify[] = ['success', 'Updated successfully'];
        
        return redirect()->route('admin.munchies.munchies-index')->withNotify($notify);
    }

    public function updateSortOrder(Request $request)
    {

        $request->validate([
            'sort_order' => 'required|integer',
        ]);

        try {
            $munchies = Munchie::findOrFail($request->status_id);
            $munchies->sort_order = $request->sort_order;
            $munchies->save();

           
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
  
        $munchie = Munchie::findOrFail($id); 
        $munchie->delete();



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



// {message: "Class "App\Http\Controllers\Admin\Munchie" not found", exception: "Error",…}
// exception
// : 
// "Error"
// file
// : 
// "/home/invoidea/public_html/lab2/outlier/application/app/Http/Controllers/Admin/BasicsController.php"
// line
// : 
// 112
// message
// : 
// "Class \"App\\Http\\Controllers\\Admin\\Munchie\" not found"
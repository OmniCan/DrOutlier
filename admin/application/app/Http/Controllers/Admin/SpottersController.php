<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\MemberOfParliament;
use App\Models\Spotter;
use App\Models\User;
use App\Models\SpottersCategory;
use App\Http\Controllers\Controller;
use App\Notifications\SendPushNotification;
use Illuminate\Notifications\Notification;
use Kutia\Larafirebase\Messages\FirebaseMessage;

class SpottersController extends Controller
{

    public function via($notifiable)
    {
        return ['firebase'];
    }


    public function index(){
        $pageTitle = 'Spotters List';
         $spotterslist = $this->spotterData();
        return view('admin.spotters.index',compact('pageTitle','spotterslist'));
    }


     protected function spotterData($scope = null)
    {

        $query = ($scope)
            ? Spotter::$scope()
            : Spotter::with(['categories']);

        $request = request();
        if ($request->search) {
            $search = $request->search;
            $query = $query->where('title', 'like', "%$search%");
        }
      $spotterslist = $query->orderBy('sort_order', 'ASC')->paginate();

        return $spotterslist;
    }



    public function create(){
        $pageTitle = 'Add Spotters';
        // Get parent categories
        $parentCategories = SpottersCategory::where('status', 1)
            ->where('parent_id', 0)
            ->orderBy('name', 'ASC')
            ->get();

        // Get all categories for hierarchical display
        $category = SpottersCategory::where('status', 1)->orderBy('parent_id', 'ASC')->orderBy('name', 'ASC')->get();

        return view('admin.spotters.create',compact('pageTitle','category','parentCategories'));
    }

    public function store(Request $request){

        $user_token = User::all();


        $deviceTokens = [];
        foreach($user_token as $token){
            array_push($deviceTokens, $token->fcm_token);
        }

        $request->validate([
            'title' => 'required',
            'sort_order' => 'required|integer',
            'content' => 'required',
            'image' => 'required'
        ]);

        $spotters = new Spotter();
        $spotters->category = $request->category;
        $spotters->title = $request->title;
        $spotters->sort_order = $request->sort_order;
        $spotters->content = $request->content;

        if ($request->hasFile('image')) {
            try {
                $spotters->image = fileUploader($request->image,getFilePath('SpottersImage'));

            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your image'];
                return back()->withNotify($notify);
            }
        }

        $spotters->save();

        (new FirebaseMessage)->withTitle('Hey, ', 'hello')->withBody('New Events Added!')->asNotification($deviceTokens);

        $notify[] = ['success', 'Added successfully'];

        return redirect()->route('admin.spotters.spotters-index')->withNotify($notify);
    }


    public function edit($id){
        $pageTitle = 'Update Spotters';

        // Get parent categories
        $parentCategories = SpottersCategory::where('status', 1)
            ->where('parent_id', 0)
            ->orderBy('name', 'ASC')
            ->get();

        // Get all categories for hierarchical display
        $category = SpottersCategory::where('status', 1)->orderBy('parent_id', 'ASC')->orderBy('name', 'ASC')->get();

        $spotters = Spotter::find($id);
        return view('admin.spotters.edit',compact('pageTitle','spotters','category','parentCategories'));
    }


    public function update(Request $request,$id){

        $request->validate([
            'title' => 'required',
            'sort_order' => 'required|integer',
            'content' => 'required',
        ]);


        $spotters = Spotter::findOrFail($id);
        $spotters->category = $request->category;
        $spotters->title = $request->title;
        $spotters->sort_order = $request->sort_order;
        $spotters->content = $request->content;

        if ($request->hasFile('image')) {
            try {
                $old =  $spotters->image;
                $spotters->image = fileUploader($request->image,getFilePath('SpottersImage'),null,$old);

            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your image'];
                return back()->withNotify($notify);
            }
        }

        $spotters->save();

        $notify[] = ['success', 'Updated successfully'];

        return redirect()->route('admin.spotters.spotters-index')->withNotify($notify);
    }

    public function updateSortOrder(Request $request)
    {

        $request->validate([
            'sort_order' => 'required|integer',
        ]);

        try {
            $spotters = Spotter::findOrFail($request->status_id);
            $spotters->sort_order = $request->sort_order;
            $spotters->save();


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

        $spotters = Spotter::findOrFail($id);
        $path = getFilePath('SpottersImage') . '/' . $spotters->image;
        fileManager()->removeFile($path);
        $spotters->delete();



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

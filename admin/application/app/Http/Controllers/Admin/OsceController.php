<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\MemberOfParliament;
use App\Models\Osce;
use App\Models\OsceCategory;
use App\Models\User;
use App\Models\OsceQuesAns;
use App\Http\Controllers\Controller;
use App\Notifications\SendPushNotification;
use Illuminate\Notifications\Notification;
use Kutia\Larafirebase\Messages\FirebaseMessage;

class OsceController extends Controller
{

    public function via($notifiable)
    {
        return ['firebase'];
    }


    public function index(){
        $pageTitle = 'Osce List';
          $osceslist= $this->osceData();

        return view('admin.osces.index',compact('pageTitle','osceslist'));
    }

    protected function osceData($scope = null){
        if ($scope) {
              $osceslist= Osce::$scope();
        }else{
              $osceslist = Osce::query();
        }

        //search
        $request = request();
        if ($request->search) {
            $search = $request->search;
             $osceslist  = $osceslist->where(function ($user) use ($search) {
                            $user->where('title', 'like', "%$search%");

                      });
        }
        return   $osceslist->orderBy('sort_order','desc')->paginate(getPaginate());
    }
    public function create(){
        $pageTitle = 'Add Osces';

        // Get parent categories (same as Spotters)
        $parentCategories = OsceCategory::where('status', 1)
            ->where(function($query) {
                $query->whereNull('parent_id')
                      ->orWhere('parent_id', 0);
            })
            ->orderBy('name', 'ASC')
            ->get();

        // Get all categories for hierarchical display
        $category = OsceCategory::where('status', 1)->orderBy('parent_id', 'ASC')->orderBy('name', 'ASC')->get();

        return view('admin.osces.create',compact('pageTitle', 'category', 'parentCategories'));
    }

    public function store(Request $request){

        $request->validate([
            'category' => 'required',
            'question' => 'required',
            'answer' => 'required',
            'image' => 'required',
            'title' => 'required',
            'sort_order' => 'required|integer',
        ]);

        $osces = new Osce();
        $osces->category = $request->category;
        $osces->title = $request->title;
        $osces->sort_order = $request->sort_order;
        $osces->content = $request->content;

        if ($request->hasFile('image')) {
            try {
                $osces->image = fileUploader($request->image,getFilePath('OsceImage'));
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your image'];
                return back()->withNotify($notify);
            }
        }
        $osces->save();

        if($request->question){
            foreach($request->question as $key=>$question){
                OsceQuesAns::create([
                    'osce_id' => $osces->id,
                    'question' => $question,
                    'answer' => $request->answer[$key]
                ]);
            }
        }


        $notify[] = ['success', 'Added successfully'];

        return redirect()->route('admin.osce.osce-index')->withNotify($notify);
    }


    public function edit($id){
        $pageTitle = 'Update Osces';

        $osces = Osce::with('question')->find($id);

        // Get parent categories (same as create)
        $parentCategories = OsceCategory::where('status', 1)
            ->where(function($query) {
                $query->whereNull('parent_id')
                      ->orWhere('parent_id', 0);
            })
            ->orderBy('name', 'ASC')
            ->get();

        // Get all categories for hierarchical display
        $category = OsceCategory::where('status', 1)->orderBy('parent_id', 'ASC')->orderBy('name', 'ASC')->get();

        return view('admin.osces.edit',compact('pageTitle','osces', 'category', 'parentCategories'));
    }


    public function update(Request $request,$id){

        $osces = Osce::findOrFail($id);

        $osces->category = $request->category;
        $osces->title = $request->title;
        $osces->content = $request->content;
    // Handle image upload
    if ($request->hasFile('image')) {
        try {
            $old = $osces->image;
            $osces->image = fileUploader($request->image, getFilePath('OsceImage'), null, $old);
        } catch (\Exception $exp) {
            $notify[] = ['error', 'Couldn\'t upload your image'];
            return back()->withNotify($notify);
        }
    }

    $osces->save();

    // Handle question-answer pairs
    if ($request->question) {
        // Get existing question-answer pairs for this osce
        $existingQAs = OsceQuesAns::where('osce_id', $osces->id)->get();
        $existingQuestions = $existingQAs->pluck('id')->toArray();

        $newQuestions = [];
        foreach ($request->question as $key => $question) {
            $answer = $request->answer[$key] ?? '';

            // Check if we need to create or update
            if (isset($request->qa_ids[$key])) {
                // Update existing question-answer pairs
                $qa = OsceQuesAns::find($request->qa_ids[$key]);
                if ($qa) {
                    $qa->update([
                        'question' => $question,
                        'answer' => $answer,
                    ]);
                    // Remove from the list of IDs to delete
                    $existingQuestions = array_diff($existingQuestions, [$qa->id]);
                }
            } else {
                // Create new question-answer pairs
                $newQuestions[] = [
                    'osce_id' => $osces->id,
                    'question' => $question,
                    'answer' => $answer

                ];
            }
        }

        // Bulk insert new question-answer pairs
        if ($newQuestions) {
            OsceQuesAns::insert($newQuestions);
        }

        // Delete old question-answer pairs that were not updated
        if ($existingQuestions) {
            OsceQuesAns::whereIn('id', $existingQuestions)->delete();
        }
    }

    // Redirect or return as needed
    $notify[] = ['success', 'OSCE updated successfully'];
    return redirect()->route('admin.osce.osce-index')->withNotify($notify);

    }

    public function updateSortOrder(Request $request)
    {

        $request->validate([
            'sort_order' => 'required|integer',
        ]);

        try {
            $osces = Osce::findOrFail($request->status_id);
            $osces->sort_order = $request->sort_order;
            $osces->save();


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

        $osces = Osce::findOrFail($id);
        $path = getFilePath('OsceImage') . '/' . $osces->image;
        fileManager()->removeFile($path);
        $osces->delete();

        OsceQuesAns::where('osce_id' , $id)->delete();

        $notify[] = ['success', 'Deleted successfully'];
        return back()->withNotify($notify);
    }
}

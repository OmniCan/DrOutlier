<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\NewOsce;
use App\Models\User;
use App\Models\NewOsceCategory;
use App\Http\Controllers\Controller;
use App\Notifications\SendPushNotification;
use Illuminate\Notifications\Notification;
use Kutia\Larafirebase\Messages\FirebaseMessage;

class NewOsceController extends Controller
{
    public function via($notifiable)
    {
        return ['firebase'];
    }

    public function index(){
        $pageTitle = 'New OSCE List';
        $oscelist = $this->newOsceData();
        return view('admin.new-osce.index', compact('pageTitle', 'oscelist'));
    }

    protected function newOsceData($scope = null)
    {
        $query = ($scope)
            ? NewOsce::$scope()
            : NewOsce::with(['categories']);

        $request = request();
        if ($request->search) {
            $search = $request->search;
            $query = $query->where('title', 'like', "%$search%");
        }
        $oscelist = $query->orderBy('sort_order', 'ASC')->paginate();

        return $oscelist;
    }

    public function create(){
        $pageTitle = 'Add New OSCE';
        // Get parent categories
        $parentCategories = NewOsceCategory::where('status', 1)
            ->where('parent_id', 0)
            ->orderBy('name', 'ASC')
            ->get();

        // Get all categories for hierarchical display
        $category = NewOsceCategory::where('status', 1)->orderBy('parent_id', 'ASC')->orderBy('name', 'ASC')->get();

        return view('admin.new-osce.create', compact('pageTitle', 'category', 'parentCategories'));
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
            'image' => 'required',
            'pdf_file' => 'nullable|mimes:pdf|max:10240' // PDF max 10MB
        ]);

        $osce = new NewOsce();
        $osce->category = $request->category;
        $osce->title = $request->title;
        $osce->sort_order = $request->sort_order;
        $osce->description = $request->description; // Optional field
        $osce->is_premium = $request->is_premium ?? 0;

        // Upload Image
        if ($request->hasFile('image')) {
            try {
                $osce->image = fileUploader($request->image, getFilePath('NewOsceImage'));
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your image'];
                return back()->withNotify($notify);
            }
        }

        // Upload PDF File
        if ($request->hasFile('pdf_file')) {
            try {
                $osce->pdf_file = fileUploader($request->pdf_file, getFilePath('NewOscePDF'));
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your PDF file'];
                return back()->withNotify($notify);
            }
        }

        $osce->save();

        (new FirebaseMessage)->withTitle('Hey, ', 'hello')->withBody('New OSCE Added!')->asNotification($deviceTokens);

        $notify[] = ['success', 'New OSCE added successfully'];

        return redirect()->route('admin.new-osce.new-osce-index')->withNotify($notify);
    }

    public function edit($id){
        $pageTitle = 'Update New OSCE';

        // Get parent categories
        $parentCategories = NewOsceCategory::where('status', 1)
            ->where('parent_id', 0)
            ->orderBy('name', 'ASC')
            ->get();

        // Get all categories for hierarchical display
        $category = NewOsceCategory::where('status', 1)->orderBy('parent_id', 'ASC')->orderBy('name', 'ASC')->get();

        $osce = NewOsce::find($id);
        return view('admin.new-osce.edit', compact('pageTitle', 'osce', 'category', 'parentCategories'));
    }

    public function update(Request $request, $id){
        $request->validate([
            'title' => 'required',
            'sort_order' => 'required|integer',
            'pdf_file' => 'nullable|mimes:pdf|max:10240' // PDF max 10MB
        ]);

        $osce = NewOsce::findOrFail($id);
        $osce->category = $request->category;
        $osce->title = $request->title;
        $osce->sort_order = $request->sort_order;
        $osce->description = $request->description; // Optional field
        $osce->is_premium = $request->is_premium ?? 0;

        // Upload Image
        if ($request->hasFile('image')) {
            try {
                $old = $osce->image;
                $osce->image = fileUploader($request->image, getFilePath('NewOsceImage'), null, $old);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your image'];
                return back()->withNotify($notify);
            }
        }

        // Upload PDF File
        if ($request->hasFile('pdf_file')) {
            try {
                $old = $osce->pdf_file;
                $osce->pdf_file = fileUploader($request->pdf_file, getFilePath('NewOscePDF'), null, $old);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your PDF file'];
                return back()->withNotify($notify);
            }
        }

        $osce->save();

        $notify[] = ['success', 'New OSCE updated successfully'];

        return redirect()->route('admin.new-osce.new-osce-index')->withNotify($notify);
    }

    public function updateSortOrder(Request $request)
    {
        $request->validate([
            'sort_order' => 'required|integer',
        ]);

        try {
            $osce = NewOsce::findOrFail($request->status_id);
            $osce->sort_order = $request->sort_order;
            $osce->save();

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
        $osce = NewOsce::findOrFail($id);

        // Delete image file
        if($osce->image) {
            $path = getFilePath('NewOsceImage') . '/' . $osce->image;
            fileManager()->removeFile($path);
        }

        // Delete PDF file
        if($osce->pdf_file) {
            $pdfPath = getFilePath('NewOscePDF') . '/' . $osce->pdf_file;
            fileManager()->removeFile($pdfPath);
        }

        $osce->delete();

        $notify[] = ['success', 'New OSCE deleted successfully'];
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

    public function togglePremium(Request $request, $id){
        try {
            $osce = NewOsce::findOrFail($id);
            $osce->is_premium = $osce->is_premium == 1 ? 0 : 1;
            $osce->save();

            return response()->json([
                'success' => true,
                'message' => 'Premium status updated successfully.',
                'is_premium' => $osce->is_premium
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update premium status.'
            ]);
        }
    }
}

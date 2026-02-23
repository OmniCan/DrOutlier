<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\AiRads;
use App\Models\User;
use App\Models\AiRadsCategory;
use App\Http\Controllers\Controller;
use App\Notifications\SendPushNotification;
use Illuminate\Notifications\Notification;
use Kutia\Larafirebase\Messages\FirebaseMessage;

class AiRadsController extends Controller
{
    public function via($notifiable)
    {
        return ['firebase'];
    }

    public function index(){
        $pageTitle = 'AI Rads List';
        $airadslist = $this->aiRadsData();
        return view('admin.ai-rads.index', compact('pageTitle', 'airadslist'));
    }

    protected function aiRadsData($scope = null)
    {
        $query = ($scope)
            ? AiRads::$scope()
            : AiRads::with(['categories']);

        $request = request();
        if ($request->search) {
            $search = $request->search;
            $query = $query->where('title', 'like', "%$search%");
        }
        $airadslist = $query->orderBy('sort_order', 'ASC')->paginate();

        return $airadslist;
    }

    public function create(){
        $pageTitle = 'Add AI Rads';
        // Get parent categories
        $parentCategories = AiRadsCategory::where('status', 1)
            ->where('parent_id', 0)
            ->orderBy('name', 'ASC')
            ->get();

        // Get all categories for hierarchical display
        $category = AiRadsCategory::where('status', 1)->orderBy('parent_id', 'ASC')->orderBy('name', 'ASC')->get();

        return view('admin.ai-rads.create', compact('pageTitle', 'category', 'parentCategories'));
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

        $aiRads = new AiRads();
        $aiRads->category = $request->category;
        $aiRads->title = $request->title;
        $aiRads->sort_order = $request->sort_order;
        $aiRads->description = $request->description; // Optional field
        $aiRads->is_premium = $request->is_premium ?? 0;

        // Upload Image
        if ($request->hasFile('image')) {
            try {
                $aiRads->image = fileUploader($request->image, getFilePath('AiRadsImage'));
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your image'];
                return back()->withNotify($notify);
            }
        }

        // Upload PDF File
        if ($request->hasFile('pdf_file')) {
            try {
                $aiRads->pdf_file = fileUploader($request->pdf_file, getFilePath('AiRadsPDF'));
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your PDF file'];
                return back()->withNotify($notify);
            }
        }

        $aiRads->save();

        (new FirebaseMessage)->withTitle('Hey, ', 'hello')->withBody('AI Rads Added!')->asNotification($deviceTokens);

        $notify[] = ['success', 'AI Rads added successfully'];

        return redirect()->route('admin.ai-rads.ai-rads-index')->withNotify($notify);
    }

    public function edit($id){
        $pageTitle = 'Update AI Rads';

        // Get parent categories
        $parentCategories = AiRadsCategory::where('status', 1)
            ->where('parent_id', 0)
            ->orderBy('name', 'ASC')
            ->get();

        // Get all categories for hierarchical display
        $category = AiRadsCategory::where('status', 1)->orderBy('parent_id', 'ASC')->orderBy('name', 'ASC')->get();

        $aiRads = AiRads::find($id);
        return view('admin.ai-rads.edit', compact('pageTitle', 'aiRads', 'category', 'parentCategories'));
    }

    public function update(Request $request, $id){
        $request->validate([
            'title' => 'required',
            'sort_order' => 'required|integer',
            'pdf_file' => 'nullable|mimes:pdf|max:10240' // PDF max 10MB
        ]);

        $aiRads = AiRads::findOrFail($id);
        $aiRads->category = $request->category;
        $aiRads->title = $request->title;
        $aiRads->sort_order = $request->sort_order;
        $aiRads->description = $request->description; // Optional field
        $aiRads->is_premium = $request->is_premium ?? 0;

        // Upload Image
        if ($request->hasFile('image')) {
            try {
                $old = $aiRads->image;
                $aiRads->image = fileUploader($request->image, getFilePath('AiRadsImage'), null, $old);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your image'];
                return back()->withNotify($notify);
            }
        }

        // Upload PDF File
        if ($request->hasFile('pdf_file')) {
            try {
                $old = $aiRads->pdf_file;
                $aiRads->pdf_file = fileUploader($request->pdf_file, getFilePath('AiRadsPDF'), null, $old);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your PDF file'];
                return back()->withNotify($notify);
            }
        }

        $aiRads->save();

        $notify[] = ['success', 'AI Rads updated successfully'];

        return redirect()->route('admin.ai-rads.ai-rads-index')->withNotify($notify);
    }

    public function updateSortOrder(Request $request)
    {
        $request->validate([
            'sort_order' => 'required|integer',
        ]);

        try {
            $aiRads = AiRads::findOrFail($request->status_id);
            $aiRads->sort_order = $request->sort_order;
            $aiRads->save();

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
        $aiRads = AiRads::findOrFail($id);

        // Delete image file
        if($aiRads->image) {
            $path = getFilePath('AiRadsImage') . '/' . $aiRads->image;
            fileManager()->removeFile($path);
        }

        // Delete PDF file
        if($aiRads->pdf_file) {
            $pdfPath = getFilePath('AiRadsPDF') . '/' . $aiRads->pdf_file;
            fileManager()->removeFile($pdfPath);
        }

        $aiRads->delete();

        $notify[] = ['success', 'AI Rads deleted successfully'];
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
            $aiRads = AiRads::findOrFail($id);
            $aiRads->is_premium = $aiRads->is_premium == 1 ? 0 : 1;
            $aiRads->save();

            return response()->json([
                'success' => true,
                'message' => 'Premium status updated successfully.',
                'is_premium' => $aiRads->is_premium
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update premium status.'
            ]);
        }
    }
}

<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\NewSpotter;
use App\Models\User;
use App\Models\NewSpottersCategory;
use App\Http\Controllers\Controller;
use App\Notifications\SendPushNotification;
use Illuminate\Notifications\Notification;
use Kutia\Larafirebase\Messages\FirebaseMessage;

class NewSpottersController extends Controller
{
    public function via($notifiable)
    {
        return ['firebase'];
    }

    public function index(){
        $pageTitle = 'New Spotters List';
        $spotterslist = $this->newSpotterData();
        return view('admin.new-spotters.index', compact('pageTitle', 'spotterslist'));
    }

    protected function newSpotterData($scope = null)
    {
        $query = ($scope)
            ? NewSpotter::$scope()
            : NewSpotter::with(['categories']);

        $request = request();
        if ($request->search) {
            $search = $request->search;
            $query = $query->where('title', 'like', "%$search%");
        }
        $spotterslist = $query->orderBy('sort_order', 'ASC')->paginate();

        return $spotterslist;
    }

    public function create(){
        $pageTitle = 'Add New Spotter';
        // Get parent categories
        $parentCategories = NewSpottersCategory::where('status', 1)
            ->where('parent_id', 0)
            ->orderBy('name', 'ASC')
            ->get();

        // Get all categories for hierarchical display
        $category = NewSpottersCategory::where('status', 1)->orderBy('parent_id', 'ASC')->orderBy('name', 'ASC')->get();

        return view('admin.new-spotters.create', compact('pageTitle', 'category', 'parentCategories'));
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

        $spotters = new NewSpotter();
        $spotters->category = $request->category;
        $spotters->title = $request->title;
        $spotters->sort_order = $request->sort_order;
        $spotters->description = $request->description; // Optional field
        $spotters->is_premium = $request->is_premium ?? 0;

        // Upload Image
        if ($request->hasFile('image')) {
            try {
                $spotters->image = fileUploader($request->image, getFilePath('NewSpottersImage'));
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your image'];
                return back()->withNotify($notify);
            }
        }

        // Upload PDF File
        if ($request->hasFile('pdf_file')) {
            try {
                $spotters->pdf_file = fileUploader($request->pdf_file, getFilePath('NewSpottersPDF'));
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your PDF file'];
                return back()->withNotify($notify);
            }
        }

        $spotters->save();

        (new FirebaseMessage)->withTitle('Hey, ', 'hello')->withBody('New Spotter Added!')->asNotification($deviceTokens);

        $notify[] = ['success', 'New Spotter added successfully'];

        return redirect()->route('admin.new-spotters.new-spotters-index')->withNotify($notify);
    }

    public function edit($id){
        $pageTitle = 'Update New Spotter';

        // Get parent categories
        $parentCategories = NewSpottersCategory::where('status', 1)
            ->where('parent_id', 0)
            ->orderBy('name', 'ASC')
            ->get();

        // Get all categories for hierarchical display
        $category = NewSpottersCategory::where('status', 1)->orderBy('parent_id', 'ASC')->orderBy('name', 'ASC')->get();

        $spotters = NewSpotter::find($id);
        return view('admin.new-spotters.edit', compact('pageTitle', 'spotters', 'category', 'parentCategories'));
    }

    public function update(Request $request, $id){
        $request->validate([
            'title' => 'required',
            'sort_order' => 'required|integer',
            'pdf_file' => 'nullable|mimes:pdf|max:10240' // PDF max 10MB
        ]);

        $spotters = NewSpotter::findOrFail($id);
        $spotters->category = $request->category;
        $spotters->title = $request->title;
        $spotters->sort_order = $request->sort_order;
        $spotters->description = $request->description; // Optional field
        $spotters->is_premium = $request->is_premium ?? 0;

        // Upload Image
        if ($request->hasFile('image')) {
            try {
                $old = $spotters->image;
                $spotters->image = fileUploader($request->image, getFilePath('NewSpottersImage'), null, $old);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your image'];
                return back()->withNotify($notify);
            }
        }

        // Upload PDF File
        if ($request->hasFile('pdf_file')) {
            try {
                $old = $spotters->pdf_file;
                $spotters->pdf_file = fileUploader($request->pdf_file, getFilePath('NewSpottersPDF'), null, $old);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your PDF file'];
                return back()->withNotify($notify);
            }
        }

        $spotters->save();

        $notify[] = ['success', 'New Spotter updated successfully'];

        return redirect()->route('admin.new-spotters.new-spotters-index')->withNotify($notify);
    }

    public function updateSortOrder(Request $request)
    {
        $request->validate([
            'sort_order' => 'required|integer',
        ]);

        try {
            $spotters = NewSpotter::findOrFail($request->status_id);
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
        $spotters = NewSpotter::findOrFail($id);

        // Delete image file
        if($spotters->image) {
            $path = getFilePath('NewSpottersImage') . '/' . $spotters->image;
            fileManager()->removeFile($path);
        }

        // Delete PDF file
        if($spotters->pdf_file) {
            $pdfPath = getFilePath('NewSpottersPDF') . '/' . $spotters->pdf_file;
            fileManager()->removeFile($pdfPath);
        }

        $spotters->delete();

        $notify[] = ['success', 'New Spotter deleted successfully'];
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
            $spotters = NewSpotter::findOrFail($id);
            $spotters->is_premium = $spotters->is_premium == 1 ? 0 : 1;
            $spotters->save();

            return response()->json([
                'success' => true,
                'message' => 'Premium status updated successfully.',
                'is_premium' => $spotters->is_premium
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update premium status.'
            ]);
        }
    }
}

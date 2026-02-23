<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\PracticalEssentials;
use App\Models\User;
use App\Models\PracticalEssentialsCategory;
use App\Http\Controllers\Controller;
use App\Notifications\SendPushNotification;
use Illuminate\Notifications\Notification;
use Kutia\Larafirebase\Messages\FirebaseMessage;

class PracticalEssentialsController extends Controller
{
    public function via($notifiable)
    {
        return ['firebase'];
    }

    public function index(){
        $pageTitle = 'Practical Essentials List';
        $practicalessentialslist = $this->practicalEssentialsData();
        return view('admin.practical-essentials.index', compact('pageTitle', 'practicalessentialslist'));
    }

    protected function practicalEssentialsData($scope = null)
    {
        $query = ($scope)
            ? PracticalEssentials::$scope()
            : PracticalEssentials::with(['categories']);

        $request = request();
        if ($request->search) {
            $search = $request->search;
            $query = $query->where('title', 'like', "%$search%");
        }
        $practicalessentialslist = $query->orderBy('sort_order', 'ASC')->paginate();

        return $practicalessentialslist;
    }

    public function create(){
        $pageTitle = 'Add Practical Essentials';
        // Get parent categories
        $parentCategories = PracticalEssentialsCategory::where('status', 1)
            ->where('parent_id', 0)
            ->orderBy('name', 'ASC')
            ->get();

        // Get all categories for hierarchical display
        $category = PracticalEssentialsCategory::where('status', 1)->orderBy('parent_id', 'ASC')->orderBy('name', 'ASC')->get();

        return view('admin.practical-essentials.create', compact('pageTitle', 'category', 'parentCategories'));
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

        $practicalEssentials = new PracticalEssentials();
        $practicalEssentials->category = $request->category;
        $practicalEssentials->title = $request->title;
        $practicalEssentials->sort_order = $request->sort_order;
        $practicalEssentials->description = $request->description; // Optional field
        $practicalEssentials->is_premium = $request->is_premium ?? 0;

        // Upload Image
        if ($request->hasFile('image')) {
            try {
                $practicalEssentials->image = fileUploader($request->image, getFilePath('PracticalEssentialsImage'));
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your image'];
                return back()->withNotify($notify);
            }
        }

        // Upload PDF File
        if ($request->hasFile('pdf_file')) {
            try {
                $practicalEssentials->pdf_file = fileUploader($request->pdf_file, getFilePath('PracticalEssentialsPDF'));
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your PDF file'];
                return back()->withNotify($notify);
            }
        }

        $practicalEssentials->save();

        (new FirebaseMessage)->withTitle('Hey, ', 'hello')->withBody('Practical Essentials Added!')->asNotification($deviceTokens);

        $notify[] = ['success', 'Practical Essentials added successfully'];

        return redirect()->route('admin.practical-essentials.practical-essentials-index')->withNotify($notify);
    }

    public function edit($id){
        $pageTitle = 'Update Practical Essentials';

        // Get parent categories
        $parentCategories = PracticalEssentialsCategory::where('status', 1)
            ->where('parent_id', 0)
            ->orderBy('name', 'ASC')
            ->get();

        // Get all categories for hierarchical display
        $category = PracticalEssentialsCategory::where('status', 1)->orderBy('parent_id', 'ASC')->orderBy('name', 'ASC')->get();

        $practicalEssentials = PracticalEssentials::find($id);
        return view('admin.practical-essentials.edit', compact('pageTitle', 'practicalEssentials', 'category', 'parentCategories'));
    }

    public function update(Request $request, $id){
        $request->validate([
            'title' => 'required',
            'sort_order' => 'required|integer',
            'pdf_file' => 'nullable|mimes:pdf|max:10240' // PDF max 10MB
        ]);

        $practicalEssentials = PracticalEssentials::findOrFail($id);
        $practicalEssentials->category = $request->category;
        $practicalEssentials->title = $request->title;
        $practicalEssentials->sort_order = $request->sort_order;
        $practicalEssentials->description = $request->description; // Optional field
        $practicalEssentials->is_premium = $request->is_premium ?? 0;

        // Upload Image
        if ($request->hasFile('image')) {
            try {
                $old = $practicalEssentials->image;
                $practicalEssentials->image = fileUploader($request->image, getFilePath('PracticalEssentialsImage'), null, $old);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your image'];
                return back()->withNotify($notify);
            }
        }

        // Upload PDF File
        if ($request->hasFile('pdf_file')) {
            try {
                $old = $practicalEssentials->pdf_file;
                $practicalEssentials->pdf_file = fileUploader($request->pdf_file, getFilePath('PracticalEssentialsPDF'), null, $old);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your PDF file'];
                return back()->withNotify($notify);
            }
        }

        $practicalEssentials->save();

        $notify[] = ['success', 'Practical Essentials updated successfully'];

        return redirect()->route('admin.practical-essentials.practical-essentials-index')->withNotify($notify);
    }

    public function updateSortOrder(Request $request)
    {
        $request->validate([
            'sort_order' => 'required|integer',
        ]);

        try {
            $practicalEssentials = PracticalEssentials::findOrFail($request->status_id);
            $practicalEssentials->sort_order = $request->sort_order;
            $practicalEssentials->save();

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
        $practicalEssentials = PracticalEssentials::findOrFail($id);

        // Delete image file
        if($practicalEssentials->image) {
            $path = getFilePath('PracticalEssentialsImage') . '/' . $practicalEssentials->image;
            fileManager()->removeFile($path);
        }

        // Delete PDF file
        if($practicalEssentials->pdf_file) {
            $pdfPath = getFilePath('PracticalEssentialsPDF') . '/' . $practicalEssentials->pdf_file;
            fileManager()->removeFile($pdfPath);
        }

        $practicalEssentials->delete();

        $notify[] = ['success', 'Practical Essentials deleted successfully'];
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
            $practicalEssentials = PracticalEssentials::findOrFail($id);
            $practicalEssentials->is_premium = $practicalEssentials->is_premium == 1 ? 0 : 1;
            $practicalEssentials->save();

            return response()->json([
                'success' => true,
                'message' => 'Premium status updated successfully.',
                'is_premium' => $practicalEssentials->is_premium
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update premium status.'
            ]);
        }
    }
}

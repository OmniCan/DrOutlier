<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\NewTableViva;
use App\Models\User;
use App\Models\NewTableVivaCategory;
use App\Http\Controllers\Controller;
use App\Notifications\SendPushNotification;
use Illuminate\Notifications\Notification;
use Kutia\Larafirebase\Messages\FirebaseMessage;

class NewTableVivaController extends Controller
{
    public function via($notifiable)
    {
        return ['firebase'];
    }

    public function index(){
        $pageTitle = 'New Table Viva List';
        $spotterslist = $this->newTableVivaData();
        return view('admin.new-table-viva.index', compact('pageTitle', 'spotterslist'));
    }

    protected function newTableVivaData($scope = null)
    {
        $query = ($scope)
            ? NewTableViva::$scope()
            : NewTableViva::with(['categories']);

        $request = request();
        if ($request->search) {
            $search = $request->search;
            $query = $query->where('title', 'like', "%$search%");
        }
        $spotterslist = $query->orderBy('sort_order', 'ASC')->paginate();

        return $spotterslist;
    }

    public function create(){
        $pageTitle = 'Add New Table Viva';
        // Get parent categories
        $parentCategories = NewTableVivaCategory::where('status', 1)
            ->where('parent_id', 0)
            ->orderBy('name', 'ASC')
            ->get();

        // Get all categories for hierarchical display
        $category = NewTableVivaCategory::where('status', 1)->orderBy('parent_id', 'ASC')->orderBy('name', 'ASC')->get();

        return view('admin.new-table-viva.create', compact('pageTitle', 'category', 'parentCategories'));
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

        $spotters = new NewTableViva();
        $spotters->category = $request->category;
        $spotters->title = $request->title;
        $spotters->sort_order = $request->sort_order;
        $spotters->description = $request->description; // Optional field
        $spotters->is_premium = $request->is_premium ?? 0;

        // Upload Image
        if ($request->hasFile('image')) {
            try {
                $spotters->image = fileUploader($request->image, getFilePath('NewTableVivaImage'));
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your image'];
                return back()->withNotify($notify);
            }
        }

        // Upload PDF File
        if ($request->hasFile('pdf_file')) {
            try {
                $spotters->pdf_file = fileUploader($request->pdf_file, getFilePath('NewTableVivaPDF'));
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your PDF file'];
                return back()->withNotify($notify);
            }
        }

        $spotters->save();

        (new FirebaseMessage)->withTitle('Hey, ', 'hello')->withBody('New Table Viva Added!')->asNotification($deviceTokens);

        $notify[] = ['success', 'New Table Viva added successfully'];

        return redirect()->route('admin.new-table-viva.new-table-viva-index')->withNotify($notify);
    }

    public function edit($id){
        $pageTitle = 'Update New Table Viva';

        // Get parent categories
        $parentCategories = NewTableVivaCategory::where('status', 1)
            ->where('parent_id', 0)
            ->orderBy('name', 'ASC')
            ->get();

        // Get all categories for hierarchical display
        $category = NewTableVivaCategory::where('status', 1)->orderBy('parent_id', 'ASC')->orderBy('name', 'ASC')->get();

        $spotters = NewTableViva::find($id);
        return view('admin.new-table-viva.edit', compact('pageTitle', 'spotters', 'category', 'parentCategories'));
    }

    public function update(Request $request, $id){
        $request->validate([
            'title' => 'required',
            'sort_order' => 'required|integer',
            'pdf_file' => 'nullable|mimes:pdf|max:10240' // PDF max 10MB
        ]);

        $spotters = NewTableViva::findOrFail($id);
        $spotters->category = $request->category;
        $spotters->title = $request->title;
        $spotters->sort_order = $request->sort_order;
        $spotters->description = $request->description; // Optional field
        $spotters->is_premium = $request->is_premium ?? 0;

        // Upload Image
        if ($request->hasFile('image')) {
            try {
                $old = $spotters->image;
                $spotters->image = fileUploader($request->image, getFilePath('NewTableVivaImage'), null, $old);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your image'];
                return back()->withNotify($notify);
            }
        }

        // Upload PDF File
        if ($request->hasFile('pdf_file')) {
            try {
                $old = $spotters->pdf_file;
                $spotters->pdf_file = fileUploader($request->pdf_file, getFilePath('NewTableVivaPDF'), null, $old);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your PDF file'];
                return back()->withNotify($notify);
            }
        }

        $spotters->save();

        $notify[] = ['success', 'New Table Viva updated successfully'];

        return redirect()->route('admin.new-table-viva.new-table-viva-index')->withNotify($notify);
    }

    public function updateSortOrder(Request $request)
    {
        $request->validate([
            'sort_order' => 'required|integer',
        ]);

        try {
            $spotters = NewTableViva::findOrFail($request->status_id);
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
        $spotters = NewTableViva::findOrFail($id);

        // Delete image file
        if($spotters->image) {
            $path = getFilePath('NewTableVivaImage') . '/' . $spotters->image;
            fileManager()->removeFile($path);
        }

        // Delete PDF file
        if($spotters->pdf_file) {
            $pdfPath = getFilePath('NewTableVivaPDF') . '/' . $spotters->pdf_file;
            fileManager()->removeFile($pdfPath);
        }

        $spotters->delete();

        $notify[] = ['success', 'New Table Viva deleted successfully'];
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
            $spotters = NewTableViva::findOrFail($id);
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

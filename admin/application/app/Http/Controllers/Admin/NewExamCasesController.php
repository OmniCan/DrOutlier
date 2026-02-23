<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\NewExamCases;
use App\Models\User;
use App\Models\NewExamCasesCategory;
use App\Http\Controllers\Controller;
use App\Notifications\SendPushNotification;
use Illuminate\Notifications\Notification;
use Kutia\Larafirebase\Messages\FirebaseMessage;

class NewExamCasesController extends Controller
{
    public function via($notifiable)
    {
        return ['firebase'];
    }

    public function index(){
        $pageTitle = 'New Exam Cases List';
        $spotterslist = $this->newExamCasesData();
        return view('admin.new-exam-cases.index', compact('pageTitle', 'spotterslist'));
    }

    protected function newExamCasesData($scope = null)
    {
        $query = ($scope)
            ? NewExamCases::$scope()
            : NewExamCases::with(['categories']);

        $request = request();
        if ($request->search) {
            $search = $request->search;
            $query = $query->where('title', 'like', "%$search%");
        }
        $spotterslist = $query->orderBy('sort_order', 'ASC')->paginate();

        return $spotterslist;
    }

    public function create(){
        $pageTitle = 'Add New Exam Case';
        // Get parent categories
        $parentCategories = NewExamCasesCategory::where('status', 1)
            ->where('parent_id', 0)
            ->orderBy('name', 'ASC')
            ->get();

        // Get all categories for hierarchical display
        $category = NewExamCasesCategory::where('status', 1)->orderBy('parent_id', 'ASC')->orderBy('name', 'ASC')->get();

        return view('admin.new-exam-cases.create', compact('pageTitle', 'category', 'parentCategories'));
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

        $spotters = new NewExamCases();
        $spotters->category = $request->category;
        $spotters->title = $request->title;
        $spotters->sort_order = $request->sort_order;
        $spotters->description = $request->description; // Optional field
        $spotters->is_premium = $request->is_premium ?? 0;

        // Upload Image
        if ($request->hasFile('image')) {
            try {
                $spotters->image = fileUploader($request->image, getFilePath('NewExamCasesImage'));
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your image'];
                return back()->withNotify($notify);
            }
        }

        // Upload PDF File
        if ($request->hasFile('pdf_file')) {
            try {
                $spotters->pdf_file = fileUploader($request->pdf_file, getFilePath('NewExamCasesPDF'));
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your PDF file'];
                return back()->withNotify($notify);
            }
        }

        $spotters->save();

        (new FirebaseMessage)->withTitle('Hey, ', 'hello')->withBody('New Exam Case Added!')->asNotification($deviceTokens);

        $notify[] = ['success', 'New Exam Case added successfully'];

        return redirect()->route('admin.new-exam-cases.new-exam-cases-index')->withNotify($notify);
    }

    public function edit($id){
        $pageTitle = 'Update New Exam Case';

        // Get parent categories
        $parentCategories = NewExamCasesCategory::where('status', 1)
            ->where('parent_id', 0)
            ->orderBy('name', 'ASC')
            ->get();

        // Get all categories for hierarchical display
        $category = NewExamCasesCategory::where('status', 1)->orderBy('parent_id', 'ASC')->orderBy('name', 'ASC')->get();

        $spotters = NewExamCases::find($id);
        return view('admin.new-exam-cases.edit', compact('pageTitle', 'spotters', 'category', 'parentCategories'));
    }

    public function update(Request $request, $id){
        $request->validate([
            'title' => 'required',
            'sort_order' => 'required|integer',
            'pdf_file' => 'nullable|mimes:pdf|max:10240' // PDF max 10MB
        ]);

        $spotters = NewExamCases::findOrFail($id);
        $spotters->category = $request->category;
        $spotters->title = $request->title;
        $spotters->sort_order = $request->sort_order;
        $spotters->description = $request->description; // Optional field
        $spotters->is_premium = $request->is_premium ?? 0;

        // Upload Image
        if ($request->hasFile('image')) {
            try {
                $old = $spotters->image;
                $spotters->image = fileUploader($request->image, getFilePath('NewExamCasesImage'), null, $old);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your image'];
                return back()->withNotify($notify);
            }
        }

        // Upload PDF File
        if ($request->hasFile('pdf_file')) {
            try {
                $old = $spotters->pdf_file;
                $spotters->pdf_file = fileUploader($request->pdf_file, getFilePath('NewExamCasesPDF'), null, $old);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your PDF file'];
                return back()->withNotify($notify);
            }
        }

        $spotters->save();

        $notify[] = ['success', 'New Exam Case updated successfully'];

        return redirect()->route('admin.new-exam-cases.new-exam-cases-index')->withNotify($notify);
    }

    public function updateSortOrder(Request $request)
    {
        $request->validate([
            'sort_order' => 'required|integer',
        ]);

        try {
            $spotters = NewExamCases::findOrFail($request->status_id);
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
        $spotters = NewExamCases::findOrFail($id);

        // Delete image file
        if($spotters->image) {
            $path = getFilePath('NewExamCasesImage') . '/' . $spotters->image;
            fileManager()->removeFile($path);
        }

        // Delete PDF file
        if($spotters->pdf_file) {
            $pdfPath = getFilePath('NewExamCasesPDF') . '/' . $spotters->pdf_file;
            fileManager()->removeFile($pdfPath);
        }

        $spotters->delete();

        $notify[] = ['success', 'New Exam Case deleted successfully'];
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
            $spotters = NewExamCases::findOrFail($id);
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

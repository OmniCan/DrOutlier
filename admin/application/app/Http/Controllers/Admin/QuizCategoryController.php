<?php

namespace App\Http\Controllers\Admin;
 
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\Question;
use App\Models\QuizaroCategory;
use Kutia\Larafirebase\Messages\FirebaseMessage;

class QuizCategoryController extends Controller
{

    public function via($notifiable)
    {
        return ['firebase'];
    }


    public function index(){
        $pageTitle = 'Quiz Category';
          $categories= $this->quizData();
       
        return view('admin.quiz_category.index',compact('pageTitle','categories'));
    }

     protected function quizData($scope = null){
        if ($scope) {
             $categories = QuizaroCategory::$scope();
        }else{
             $categories = QuizaroCategory::query();
        }

        
        $request = request();
        if ($request->search) {
            $search = $request->search;
             $categories=  $categories->where(function ($user) use ($search) {
                            $user->where('name', 'like', "%$search%");
                      });
        }
        return  $categories->where('status',1)->paginate(getPaginate());
    }

    public function create(){
        $pageTitle = 'Add Category';
        return view('admin.quiz_category.create', compact('pageTitle'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'image' => 'nullable|image',
            'status' => 'required|boolean',
           
        ]);

        try {
        $category = new QuizaroCategory();
        $category->name = $request->name;
        $category->status = $request->status;

        if ($request->hasFile('image')) {
            try {  
                $category->image = fileUploader($request->image,getFilePath('QuestionsImage')); 

            } catch (\Exception $exp) {  
                $notify[] = ['error', 'Couldn\'t upload your image'];
                return back()->withNotify($notify);
            }
        }
        $category->save();

            session()->flash('success', __('Category added successfully!'));
            return redirect()->route('admin.quiz.category.index');
        } catch (\Exception $e) {
            session()->flash('error', __('Something went wrong. Please try again.'));
            return redirect()->back()->withInput();
        }
    }

    public function edit($id)
    {
        $pageTitle = 'Update Category';
        $categories = QuizaroCategory::findOrFail($id);
        return view('admin.quiz_category.edit', compact('pageTitle','categories'));
    }
    
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'image' => 'nullable|image',
            'status' => 'required|boolean',
           
        ]);

        try {
        $category = QuizaroCategory::find($id);
        $category->name = $request->name;
        $category->status = $request->status;

        if ($request->hasFile('image')) {
            try {  
                $category->image = fileUploader($request->image,getFilePath('QuestionsImage')); 

            } catch (\Exception $exp) {  
                $notify[] = ['error', 'Couldn\'t upload your image'];
                return back()->withNotify($notify);
            }
        }
        $category->save();

            session()->flash('success', __('Category updated successfully!'));
            return redirect()->route('admin.quiz.category.index');
        } catch (\Exception $e) {
            session()->flash('error', __('Something went wrong. Please try again.'));
            return redirect()->back()->withInput();
        }
    }
    
    


    public function delete($id)
    {
        try {
            $categories = QuizaroCategory::findOrFail($id);

            $categories->delete();
            $notify[] = ['success', 'Question deleted successfully'];
            return back()->withNotify($notify);
        } catch (\Exception $e) {
            $notify[] = ['error', 'Something went wrong. Please try again.'];
            return back()->withNotify($notify);
        }
    }
    
    
}

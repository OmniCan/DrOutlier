<?php

namespace App\Http\Controllers\Admin;
 
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\Question;
use App\Models\QuizaroCategory;
use App\Models\QuizaroQuiz;
use Kutia\Larafirebase\Messages\FirebaseMessage;

class QuizController extends Controller
{

    public function via($notifiable)
    {
        return ['firebase'];
    }
   public function index(){
        $pageTitle = 'Quiz List';
        $qustionList = $this->questionData();
        return view('admin.quiz.index',compact('pageTitle','qustionList'));
    }

  protected function questionData($scope = null)
    {

        $query = ($scope)
            ? Question::$scope()
            : Question::with(['quiz']);

        $request = request();
        if ($request->search) {
            $search = $request->search;
            $query = $query->where('question_text', 'like', "%$search%");
        }
         $qustionList = $query->orderBy('sort_order', 'ASC')->paginate();
        return  $qustionList;
    }
    public function create(){
        $pageTitle = 'Add Quiz';
        $categories = QuizaroQuiz::with('category')->get();
        return view('admin.quiz.create', compact('pageTitle', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'question_text' => 'required|string',
            'explanation' => 'required|string',
            'status' => 'nullable|boolean',
            'image' => 'nullable|image',
            'category_id' => 'required|exists:quizaro_quiz,id',
            'options' => 'required|array|min:1|max:4',
            'options.*.option_text' => 'required|string',
            'options.*.explanation' => 'nullable|string',
            'options.*.is_correct' => 'nullable|boolean',

        ]);
    
        try {
            $quiz = new Question();
            $quiz->quiz_id = $request->category_id;
            $quiz->question_text = $request->question_text;
            $quiz->explanation = $request->explanation;
            $quiz->status = $request->status;
            $quiz->sort_order = 0; 
    
            if ($request->hasFile('image')) {
                try {  
                    $quiz->image = fileUploader($request->image,getFilePath('QuestionsImage')); 
    
                } catch (\Exception $exp) {  
                    $notify[] = ['error', 'Couldn\'t upload your image'];
                    return back()->withNotify($notify);
                }
            }
   
            $quiz->save();
    
            foreach ($request->options as $key => $option) {
                $quizOption = new Answer();
                $quizOption->question_id = $quiz->id;
                $quizOption->option_text = $option['option_text'];
               
                $quizOption->is_correct = isset($option['is_correct']) ? 1 : 0;
    
                $quizOption->save();
            }
    
            $notify[] = ['success', 'Added successfully'];
            return redirect()->route('admin.quiz.index')->withNotify($notify);

        } catch (\Exception $e) {
            $notify[] = ['error', 'Something went wrong. Please try again.'];
            return redirect()->back()->withNotify($notify);
        }
    }

    public function edit($id)
    {
        $pageTitle = 'Update Quiz';
        $question = Question::with('answers')->findOrFail($id);
        $categories = QuizaroQuiz::with('category')->get();
        return view('admin.quiz.edit', compact('pageTitle', 'question', 'categories'));
    }
    
    public function update(Request $request, $id)
    {
        $request->validate([
            'question_text' => 'required|string',
            'status' => 'nullable|boolean',
            'image' => 'nullable|image',
            'category_id' => 'required|exists:quizaro_quiz,id',
            'options' => 'required|array|min:1|max:4',
            'options.*.option_text' => 'required|string',
            'options.*.explanation' => 'nullable|string',
            'options.*.is_correct' => 'nullable|boolean',
        ]);
    
        try {
            $quiz = Question::findOrFail($id);
            $quiz->quiz_id = $request->category_id; 
            $quiz->question_text = $request->question_text;
             $quiz->explanation = $request->explanation;
            $quiz->status = $request->status;
    
            if ($request->hasFile('image')) {
                try {  
                    $quiz->image = fileUploader($request->image,getFilePath('QuestionsImage')); 
    
                } catch (\Exception $exp) {  
                    $notify[] = ['error', 'Couldn\'t upload your image'];
                    return back()->withNotify($notify);
                }
            }
    
            $quiz->save();
    
            Answer::where('question_id', $quiz->id)->delete();
    
            foreach ($request->options as $key => $option) {
                $quizOption = new Answer();
                $quizOption->question_id = $quiz->id;
                $quizOption->option_text = $option['option_text'];
              
                $quizOption->is_correct = isset($option['is_correct']) ? 1 : 0;
    
                $quizOption->save();
            }
    
            $notify[] = ['success', 'Update successfully'];
            return redirect()->route('admin.quiz.index')->withNotify($notify);

        } catch (\Exception $e) {
            $notify[] = ['error', 'Something went wrong. Please try again.'. $e->getMessage()];
            return redirect()->back()->withNotify($notify);
        }
            
    }
    
    

    public function updateSortOrder(Request $request)
    {

        $request->validate([
            'sort_order' => 'required|integer',
        ]);

        try {
            $question = Question::findOrFail($request->status_id);
            $question->sort_order = $request->sort_order;
            $question->save();

           
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


    public function delete($id)
    {
        try {
            $question = Question::findOrFail($id);
            Answer::where('question_id', $question->id)->delete();
            if (!empty($question->image)) {
                $path = getFilePath('SpottersImage') . '/' . $question->image;
                fileManager()->removeFile($path);
            }
    
            $question->delete();
            $notify[] = ['success', 'Question deleted successfully'];
            return back()->withNotify($notify);
        } catch (\Exception $e) {
            $notify[] = ['error', 'Something went wrong. Please try again.'];
            return back()->withNotify($notify);
        }
    }

    public function quizIndex(){
        $pageTitle = 'Quizzes';
       $categories = $this->quiz();
      return view('admin.quiz_page.index',compact('pageTitle','categories'));
    }
     protected function quiz($scope = null){
        if ($scope) {
            $categories = QuizaroQuiz::$scope()->with('category');
        }else{
            $categories = QuizaroQuiz::with('category');
        }

        //search
        $request = request();
        if ($request->search) {
            $search = $request->search;
           $categories  = $categories->where(function ($user) use ($search) {
                            $user->where('name', 'like', "%$search%");
                      });
        }
        return $categories->orderBy('id','desc')->paginate(getPaginate());
    }

    public function quizCreate(){
        $pageTitle = 'Add Quiz';
        $categories = QuizaroCategory::all();
        return view('admin.quiz_page.create', compact('pageTitle', 'categories'));
    }

    public function quizStore(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:quizaro_categories,id',
            'name' => 'required',
            'image' => 'nullable|image'
           
        ]);

        try {
        $category = new QuizaroQuiz();
        $category->category_id = $request->category_id;
        $category->name = $request->name;

        if ($request->hasFile('image')) {
            try {  
                $category->image = fileUploader($request->image,getFilePath('QuestionsImage')); 

            } catch (\Exception $exp) {  
                $notify[] = ['error', 'Couldn\'t upload your image'];
                return back()->withNotify($notify);
            }
        }
        $category->save();

        $notify[] = ['success', 'Added successfully'];
        return redirect()->route('admin.quiz.quiz.index')->withNotify($notify);

    } catch (\Exception $e) {
        $notify[] = ['error', 'Something went wrong. Please try again.'];
        return redirect()->back()->withNotify($notify);
    }
       
    }

    public function quizEdit($id)
    {
        $pageTitle = 'Update Quiz';
        $quizes = QuizaroQuiz::findOrFail($id);
        $categories = QuizaroCategory::all();
        return view('admin.quiz_page.edit', compact('pageTitle','quizes','categories'));
    }
    
    public function quizUpdate(Request $request, $id)
    {
        $request->validate([
            'category_id' => 'required|exists:quizaro_categories,id',
            'name' => 'required',
            'image' => 'nullable|image'
           
        ]);

        try {
        $category = QuizaroQuiz::find($id);
        $category->category_id = $request->category_id;
        $category->name = $request->name;

        if ($request->hasFile('image')) {
            try {  
                $category->image = fileUploader($request->image,getFilePath('QuestionsImage')); 

            } catch (\Exception $exp) {  
                $notify[] = ['error', 'Couldn\'t upload your image'];
                return back()->withNotify($notify);
            }
        }
        $category->save();


        $notify[] = ['success', 'Updated successfully'];
        return redirect()->route('admin.quiz.quiz.index')->withNotify($notify);

    } catch (\Exception $e) {
        $notify[] = ['error', 'Something went wrong. Please try again.'];
        return redirect()->back()->withNotify($notify);
    }
       
    }
    
   public function quizDelete($id)
    {
        try {
            $categories = QuizaroQuiz::findOrFail($id);

            $categories->delete();
            $notify[] = ['success', 'Quiz deleted successfully'];
            return back()->withNotify($notify);
        } catch (\Exception $e) {
            $notify[] = ['error', 'Something went wrong. Please try again.'];
            return back()->withNotify($notify);
        }
    }

    // Bulk Question Upload Methods
    public function bulkUpload()
    {
        $pageTitle = 'Bulk Question Upload';
        return view('admin.quiz.bulk-upload', compact('pageTitle'));
    }

    public function downloadTemplate()
    {
        try {
            $importer = new \App\Services\BulkQuestionImporter();
            $spreadsheet = $importer->generateTemplate();

            $filename = 'quiz-questions-template-' . date('Y-m-d') . '.xlsx';

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;
        } catch (\Exception $e) {
            $notify[] = ['error', 'Failed to generate template: ' . $e->getMessage()];
            return back()->withNotify($notify);
        }
    }

    public function getQuizList()
    {
        try {
            $quizzes = QuizaroQuiz::with('category')
                ->orderBy('name', 'ASC')
                ->get()
                ->map(function($quiz) {
                    return [
                        'id' => $quiz->id,
                        'name' => $quiz->name,
                        'category' => $quiz->category ? $quiz->category->name : 'No Category'
                    ];
                });

            return response()->json([
                'success' => true,
                'quizzes' => $quizzes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function parseBulkQuestions(Request $request)
    {
        try {
            $request->validate([
                'excel_file' => 'required|file|mimes:xlsx,xls|max:10240',
                'quiz_id' => 'required|exists:quizaro_quiz,id'
            ]);

            $file = $request->file('excel_file');
            $quizId = $request->quiz_id;

            $importer = new \App\Services\BulkQuestionImporter();
            $result = $importer->parseExcelFile($file->getRealPath());

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to parse Excel file',
                    'errors' => $result['errors']
                ], 400);
            }

            // Store in session for later submission
            session([
                'bulk_upload_data' => [
                    'quiz_id' => $quizId,
                    'questions' => $result['questions'],
                    'timestamp' => time()
                ]
            ]);

            $validCount = count(array_filter($result['questions'], function($q) {
                return empty($q['errors']);
            }));

            return response()->json([
                'success' => true,
                'data' => [
                    'total' => count($result['questions']),
                    'valid' => $validCount,
                    'invalid' => count($result['questions']) - $validCount,
                    'questions' => $result['questions'],
                    'errors' => $result['errors'],
                    'warnings' => $result['warnings']
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function submitBulkQuestions(Request $request)
    {
        try {
            $uploadData = session('bulk_upload_data');

            if (!$uploadData) {
                return response()->json([
                    'success' => false,
                    'message' => 'No upload data found. Please upload file again.'
                ], 400);
            }

            $quizId = $uploadData['quiz_id'];
            $questions = $request->questions ?? $uploadData['questions'];

            $insertedCount = 0;
            $errorCount = 0;

            foreach ($questions as $questionData) {
                // Skip questions with errors
                if (!empty($questionData['errors'])) {
                    $errorCount++;
                    continue;
                }

                try {
                    // Insert question
                    $question = new Question();
                    $question->quiz_id = $quizId;
                    $question->question_text = $questionData['question_text'];
                    $question->content = $questionData['explanation'] ?? '';
                    $question->image = $questionData['question_image'];
                    $question->sort_order = $questionData['sort_order'] ?? 0;
                    $question->status = 1;
                    $question->save();

                    // Insert options/answers
                    foreach ($questionData['options'] as $option) {
                        $answer = new Answer();
                        $answer->question_id = $question->id;
                        $answer->option_text = $option['option_text'];
                        $answer->explanation = $questionData['explanation'] ?? '';
                        $answer->is_correct = $option['is_correct'];
                        $answer->save();
                    }

                    $insertedCount++;
                } catch (\Exception $e) {
                    error_log("Failed to insert question: " . $e->getMessage());
                    $errorCount++;
                }
            }

            // Clear session data
            session()->forget('bulk_upload_data');

            return response()->json([
                'success' => true,
                'message' => "$insertedCount questions uploaded successfully",
                'data' => [
                    'inserted' => $insertedCount,
                    'errors' => $errorCount
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
    

}

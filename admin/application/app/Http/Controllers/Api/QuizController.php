<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\QuizaroCategory;
use App\Models\QuizaroQuiz;
use App\Models\Question;
use App\Models\Answer;
use App\Http\Controllers\Controller;
use App\Models\QuizBookmark;
use App\Models\QuizResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class QuizController extends Controller
{

    public function categoryList(Request $request)
    {
        try {
            $userId = Auth::id();
            $quizStatusFilter = $request->query('quiz_status');

            $statusMap = [
                'completed' => 1,
                'paused' => 2,
                'unattempted' => 0
            ];

            $statusFilterValue = isset($statusMap[$quizStatusFilter]) ? $statusMap[$quizStatusFilter] : null;

            $categoriesQuery = QuizaroCategory::with(['quizzes' => function ($query) use ($userId) {
                $query->with('questions');
            }]);

            $categories = $categoriesQuery->get();

            foreach ($categories as $category) {
                $category->image_url = getImage(getFilePath('QuestionsImage') . '/' . $category->image);
                $totalQuestions = 0;
                $unattemptedQuestionsCount = 0;
                $filteredQuizzes = [];

                foreach ($category->quizzes as $quiz) {

                    $quizResponses = QuizResponse::where('quiz_id', $quiz->id)
                        ->where('user_id', $userId)
                        ->get();



                    $quizStatus = $quizResponses->isNotEmpty() ? $quizResponses->max('quiz_status') : 0;

                    if ($statusFilterValue !== null && $quizStatus !== $statusFilterValue) {
                        continue;
                    }

                    $filteredQuizzes[] = $quiz;

                    $quiz->total_questions = $quiz->questions->count();
                    $totalQuestions += $quiz->total_questions;

                    $attemptedQuestionIds = $quizResponses->where('status', 1)->pluck('question_id')->unique()->toArray();
                    $quiz->unattemptedQuestionsCount = $quiz->total_questions - count($attemptedQuestionIds);
                    $unattemptedQuestionsCount += $quiz->unattemptedQuestionsCount;

                    $quiz->quiz_status = $quizStatus;

                    foreach ($quiz->questions as $question) {
                        if ($question->image) {
                            $question->image_url = getImage(getFilePath('QuestionsImage') . '/' . $question->image);
                        }
                    }
                }

                $category->quizzes = $this->mapQuizDetails($filteredQuizzes);
                $category->total_questions = $totalQuestions;
                $category->unattempted_questions_count = $unattemptedQuestionsCount;
            }

            return response()->json([
                'success' => true,
                'message' => 'Data fetched successfully.',
                'data' => $categories
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function mapQuizDetails($quizzes)
    {
        foreach ($quizzes as $quiz) {
            $quiz->quiz_status = $quiz->quiz_status ?? 0;
            $quiz->image_url = getImage(getFilePath('QuestionsImage') . '/' . $quiz->image);

            foreach ($quiz->questions as $question) {
                if ($question->image) {
                    $question->image_url = getImage(getFilePath('QuestionsImage') . '/' . $question->image);
                }
            }
        }

        return $quizzes;
    }



    public function getQuizById($quizId)
    {
        try {
            $quiz = QuizaroQuiz::with('questions.answers')->find($quizId);

            if (!$quiz) {
                return response()->json([
                    'success' => false,
                    'message' => 'Quiz not found.'
                ], 404);
            }

            if ($quiz->image) {
                $quiz->image_url = getImage(getFilePath('QuestionsImage') . '/' . $quiz->image);
            }
            foreach ($quiz->questions as $question) {
                if ($question->image) {
                    $question->image_url = getImage(getFilePath('QuestionsImage') . '/' . $question->image);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Data fetched successfully....',
                'data' => $quiz
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getQuestionById($questionId)
    {
        try {
            $question = Question::with('answers')->find($questionId);

            if (!$question) {
                return response()->json([
                    'success' => false,
                    'message' => 'Question not found.'
                ], 404);
            }

            if ($question->image) {
                $question->image_url = getImage(getFilePath('QuestionsImage') . '/' . $question->image);
            }

            return response()->json([
                'success' => true,
                'message' => 'Data fetched successfully....',
                'data' => $question
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function changeQuizBookmark(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'quiz_id' => 'required',
            'question_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $bookmark = QuizBookmark::where('user_id', $request->user_id)
            ->where('quiz_id', $request->quiz_id)
            ->where('question_id', $request->question_id)
            ->first();

        if ($bookmark) {
            $bookmark->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Unsaved successfully !!',
            ]);
        } else {
            $newBookmark = QuizBookmark::create([
                'user_id' => $request->user_id,
                'quiz_id' => $request->quiz_id,
                'question_id' => $request->question_id,
            ]);
            return response()->json([
                'status' => 'success',
                'message' => 'Saved successfully !!',
                'data' => [
                    'list' => $newBookmark,
                ]
            ]);
        }
    }

    public function getQuizBookmarks(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $bookmarks = QuizBookmark::where('user_id', $request->user_id)
            ->with(['quiz', 'question.answers'])
            ->paginate(20);

        foreach ($bookmarks as $bookmark) {
            if ($bookmark->quiz && $bookmark->quiz->image) {
                $bookmark->quiz->image_url = getImage(getFilePath('QuestionsImage') . '/' . $bookmark->quiz->image);
            }
            if ($bookmark->question && $bookmark->question->image) {
                $bookmark->question->image_url = getImage(getFilePath('QuestionsImage') . '/' . $bookmark->question->image);
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'list' => $bookmarks,
            ]
        ]);
    }
}

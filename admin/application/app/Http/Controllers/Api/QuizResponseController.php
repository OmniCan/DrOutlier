<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\QuizResponse;
use App\Models\QuizaroQuiz;
use App\Models\Answer;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Models\Question;
use Illuminate\Support\Facades\Auth;

class QuizResponseController extends Controller
{

    public function submitQuizResponse(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'quiz_id' => 'required|exists:quizaro_quiz,id',
                'responses' => 'required|array',
                'responses.*.question_id' => 'required|exists:questions,id',
                'responses.*.selected_answer_id' => 'required|exists:answers,id',
                'responses.*.status' => 'required|in:attempted,skipped,paused',
            ]);
    
            $userId = $request->user_id;
            $quizId = $request->quiz_id;
    
            $newQuestionIds = array_column($request->responses, 'question_id');
    
            $existingResponses = QuizResponse::where('user_id', $userId)
                ->where('quiz_id', $quizId)
                ->exists();


            
    
        
                if ($existingResponses) {
                    QuizResponse::where('user_id', $userId)
                        ->where('quiz_id', $quizId)
                        ->delete();
                }
           
            foreach ($request->responses as $response) {
                $correctAnswer = Answer::where('question_id', $response['question_id'])
                    ->where('is_correct', 1)
                    ->first();
    
                $isCorrect = $correctAnswer && $correctAnswer->id == $response['selected_answer_id'];
                $status = $this->mapStatusToNumeric($response['status']);
    
                QuizResponse::create([
                    'user_id' => $userId,
                    'quiz_id' => $quizId,
                    'question_id' => $response['question_id'],
                    'selected_answer_id' => $response['selected_answer_id'],
                    'is_correct' => $isCorrect,
                    'status' => (int)$status,
                    'quiz_status' => $this->determineQuizStatus($quizId, $userId, $request->responses)
                ]);
            }
    
            $scoreData = $this->calculateScore($userId, $quizId);
    
            return response()->json([
                'success' => true,
                'message' => 'Quiz responses submitted successfully.',
                'data' => $scoreData
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function determineQuizStatus($quizId, $userId, $responses)
    {
        $totalQuestions = Question::where('quiz_id', $quizId)->count();
        $attemptedCount = QuizResponse::where('user_id', $userId)
            ->where('quiz_id', $quizId)
            ->where('status', 1)
            ->count();

        $submittedCount = count(array_filter($responses, fn($r) => $r['status'] === 'attempted'));

        if ($attemptedCount + $submittedCount >= $totalQuestions) {
            return 1; // Completed
        } elseif ($submittedCount > 0) {
            return 2; // Paused
        }
        return 0; // Unattempted
    }

    private function calculateScore($userId, $quizId)
    {
        $responses = QuizResponse::where('user_id', $userId)
            ->where('quiz_id', $quizId)
            ->get();

        $totalQuestions = $responses->count();
        $correctAnswers = $responses->where('is_correct', 1)->count();
        $incorrectAnswers = $totalQuestions - $correctAnswers;
        $scorePercentage = $totalQuestions > 0 ? ($correctAnswers / $totalQuestions) * 100 : 0;

        return [
            'score' => $correctAnswers,
            'total_questions' => $totalQuestions,
            'correct_answers' => $correctAnswers,
            'incorrect_answers' => $incorrectAnswers,
            'score_percentage' => round($scorePercentage, 2)
        ];
    }

    private function mapStatusToNumeric($status)
    {
        switch ($status) {
            case 'attempted':
                return 1;
            case 'skipped':
                return 0;
            case 'paused':
                return 2;
            default:
                return 0;
        }
    }

    public function getQuizResult(Request $request)
    {
        try {
            $userId = Auth::id();
            $quizId = $request->quiz_id;

            $quiz = QuizaroQuiz::find($quizId);
            if (!$quiz) {
                return response()->json([
                    'success' => false,
                    'message' => 'Quiz not found.'
                ], 404);
            }

            $quizQuestions = Question::where('quiz_id', $quizId)->get();
            if ($quizQuestions->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No questions found for this quiz.'
                ], 404);
            }

            $responses = QuizResponse::where('user_id', $userId)
                ->where('quiz_id', $quizId)
                ->with('question', 'selectedAnswer')
                ->get();

            if ($responses->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No responses found for this quiz.'
                ], 404);
            }

            $totalQuestions = $quizQuestions->count();
            $attemptedQuestions = $responses->where('status', 1)->count();
            $skippedQuestions = $responses->where('status', 0)->count();
            $pausedQuestions = $responses->where('status', 2)->count();

            $unattemptedQuestions = $totalQuestions - $attemptedQuestions;

            $correctAnswers = $responses->where('is_correct', 1)->count();
            $incorrectAnswers = $totalQuestions - $correctAnswers;
            $scorePercentage = $totalQuestions > 0 ? ($correctAnswers / $totalQuestions) * 100 : 0;

            return response()->json([
                'success' => true,
                'message' => 'Quiz results fetched successfully.',
                'data' => [
                    'quiz' => [
                        'id' => $quiz->id,
                        'title' => $quiz->name,
                        'status' => $quiz->status,
                        'created_at' => $quiz->created_at,
                        'updated_at' => $quiz->updated_at,

                    ],
                    'total_questions' => $totalQuestions,
                    'attempted_questions' => $attemptedQuestions,
                    'unattempted_questions' => $unattemptedQuestions,
                    'skipped_questions' => $skippedQuestions,
                    'paused_questions' => $pausedQuestions,
                    'correct_answers' => $correctAnswers,
                    'incorrect_answers' => $incorrectAnswers,
                    'score_percentage' => round($scorePercentage, 2),
                    'responses' => $responses
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function changeQuizStatus(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'quiz_id' => 'required|exists:quizaro_quiz,id'
            ]);
    
            $responses = QuizResponse::where('user_id', $request->user_id)
                ->where('quiz_id', $request->quiz_id)
                ->get();
    
            if ($responses->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No responses found for this quiz.'
                ], 404);
            }
    
            $status = $this->mapStatusToNumerical($request->status);
    
            $unattemptedCount = $responses->where('status', 0)->count();
    
            if ($unattemptedCount > 0) {
                foreach ($responses as $response) {
                    $response->quiz_status = 2; 
                    $response->save();
                }
            } else {
            
                foreach ($responses as $response) {
                    $response->quiz_status = $status;
                    $response->save();
                }
            }
    
            $allUnattempted = $responses->where('status', 0)->count() == $responses->count();
    
            if ($allUnattempted) {
                QuizResponse::where('user_id', $request->user_id)
                    ->where('quiz_id', $request->quiz_id)
                    ->update(['status' => 0]);
            }
    
            return response()->json([
                'success' => true,
                'message' => 'Quiz status updated successfully.',
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    

    private function mapStatusToNumerical($status)
    {
        switch ($status) {
            case 'completed':
                return 1;
            case 'paused':
                return 2;
            default:
                return 0;
        }
    }

}

<?php  

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizaroQuiz extends Model
{
    protected $table = 'quizaro_quiz';

    public function category()
    {
        return $this->belongsTo(QuizaroCategory::class, 'category_id');
    }

    public function questions()
    {
        return $this->hasMany(Question::class, 'quiz_id');
    }


    public function quizResponses()
    {
        return $this->hasMany(QuizResponse::class, 'quiz_id', 'id');
    }
}

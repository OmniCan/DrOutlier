<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $table = 'questions';

    protected $fillable = [
        'question_text', 'content', 'image', 'sort_order', 'status'
    ];


    public function quiz()
    {
        return $this->belongsTo(QuizaroQuiz::class, 'quiz_id');
    }

    public function answers()
    {
        return $this->hasMany(Answer::class, 'question_id');
    }

    public function quizResponses()
    {
        return $this->hasMany(QuizResponse::class, 'question_id');
    }
}

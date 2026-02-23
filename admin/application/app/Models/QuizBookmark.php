<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizBookmark extends Model
{
    use HasFactory;

    protected $table = 'quiz_bookmarks';

    protected $fillable = [
        'user_id',
        'quiz_id',
        'question_id',
    ];

    /**
     * Get the user that owns the bookmark.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the quiz associated with the bookmark.
     */
    public function quiz()
    {
        return $this->belongsTo(QuizaroQuiz::class, 'quiz_id');
    }

    /**
     * Get the question associated with the bookmark.
     */
    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }
}
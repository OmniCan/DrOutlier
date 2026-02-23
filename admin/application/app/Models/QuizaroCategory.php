<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizaroCategory extends Model
{
    use HasFactory;

    protected $table = 'quizaro_categories'; 

    protected $fillable = [
        'name', 'status', 'image'
    ];

    public function quizzes()
    {
        return $this->hasMany(QuizaroQuiz::class, 'category_id');
    }

  
}

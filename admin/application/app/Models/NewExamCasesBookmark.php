<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewExamCasesBookmark extends Model
{
    protected $table = 'new_exam_cases_bookmarks';

    // The attributes that are mass assignable
    protected $fillable = [
        'user_id',
        'item_id',
    ];
}

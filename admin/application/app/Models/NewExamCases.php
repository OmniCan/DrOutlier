<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewExamCases extends Model
{
    protected $table = 'new_exam_cases';

    protected $fillable = [
        'category',
        'title',
        'sort_order',
        'image',
        'description',
        'pdf_file',
        'is_premium'
    ];

    public function categories()
    {
        return $this->belongsTo(NewExamCasesCategory::class, 'category', 'id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewExamCasesCategory extends Model
{
    protected $table = 'new_exam_cases_categories';

    protected $fillable = [
        'name',
        'parent_id',
        'color',
        'status',
        'is_premium'
    ];

    public function children()
    {
        return $this->hasMany(NewExamCasesCategory::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(NewExamCasesCategory::class, 'parent_id');
    }

    public function items()
    {
        return $this->hasMany(NewExamCases::class, 'category');
    }
}

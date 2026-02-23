<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiRads extends Model
{
    protected $table = 'ai_rads';

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
        return $this->belongsTo(AiRadsCategory::class, 'category', 'id');
    }
}

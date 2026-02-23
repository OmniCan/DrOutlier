<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PracticalEssentials extends Model
{
    protected $table = 'practical_essentials';

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
        return $this->belongsTo(PracticalEssentialsCategory::class, 'category', 'id');
    }
}

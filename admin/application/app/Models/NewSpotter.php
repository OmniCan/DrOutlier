<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewSpotter extends Model
{
    protected $table = 'new_spotters';

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
        return $this->belongsTo(NewSpottersCategory::class, 'category', 'id');
    }
}

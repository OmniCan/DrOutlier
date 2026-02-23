<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewOsce extends Model
{
    protected $table = 'new_osce';

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
        return $this->belongsTo(NewOsceCategory::class, 'category', 'id');
    }
}

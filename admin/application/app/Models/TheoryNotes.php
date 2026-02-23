<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TheoryNotes extends Model
{
    protected $table = 'theory_notes';

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
        return $this->belongsTo(TheoryNotesCategory::class, 'category', 'id');
    }
}

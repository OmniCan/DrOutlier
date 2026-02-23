<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TheoryNotesCategory extends Model
{
    protected $table = 'theory_notes_categories';

    protected $fillable = [
        'name',
        'parent_id',
        'color',
        'status',
        'is_premium'
    ];

    public function children()
    {
        return $this->hasMany(TheoryNotesCategory::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(TheoryNotesCategory::class, 'parent_id');
    }

    public function items()
    {
        return $this->hasMany(TheoryNotes::class, 'category');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TheoryNotesBookmark extends Model
{
    protected $table = 'theory_notes_bookmarks';

    // The attributes that are mass assignable
    protected $fillable = [
        'user_id',
        'item_id',
    ];
}

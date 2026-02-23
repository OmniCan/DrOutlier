<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NoteBookmark extends Model
{

    protected $table = 'note_bookmarks';

    // The attributes that are mass assignable
    protected $fillable = [
        'user_id',
        'blog_id',
    ];
}

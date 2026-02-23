<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WatchAndLearnBookmark extends Model
{

    protected $table = 'watch_and_learn_bookmarks';

    // The attributes that are mass assignable
    protected $fillable = [
        'user_id',
        'watch_id',
    ];
}

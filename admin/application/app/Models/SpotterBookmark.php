<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpotterBookmark extends Model
{

    protected $table = 'spotter_bookmarks';

    // The attributes that are mass assignable
    protected $fillable = [
        'user_id',
        'spotter_id',
    ];
}

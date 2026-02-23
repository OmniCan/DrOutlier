<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BasicBookmark extends Model
{

    protected $table = 'basic_bookmarks';

    // The attributes that are mass assignable
    protected $fillable = [
        'user_id',
        'basic_id',
    ];
}

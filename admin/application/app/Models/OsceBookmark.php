<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OsceBookmark extends Model
{

    protected $table = 'osce_bookmarks';

    // The attributes that are mass assignable
    protected $fillable = [
        'user_id',
        'osce_id',
    ];
}

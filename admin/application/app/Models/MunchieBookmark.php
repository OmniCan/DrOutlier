<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MunchieBookmark extends Model
{

    protected $table = 'munchie_bookmarks';

    // The attributes that are mass assignable
    protected $fillable = [
        'user_id',
        'munchie_id',
    ];
}

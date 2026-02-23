<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewTableVivaBookmark extends Model
{
    protected $table = 'new_table_viva_bookmarks';

    // The attributes that are mass assignable
    protected $fillable = [
        'user_id',
        'item_id',
    ];
}

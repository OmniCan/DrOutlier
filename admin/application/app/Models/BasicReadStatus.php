<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BasicReadStatus extends Model
{
    protected $table = 'basic_read_status';

    protected $fillable = [
        'user_id',
        'category',
        'read_basic',
    ];
}

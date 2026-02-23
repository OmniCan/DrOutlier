<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MunchieReadStatus extends Model
{
    protected $table = 'munchie_read_status';

    protected $fillable = [
        'user_id',
        'category',
        'read_munchie',
    ];
}

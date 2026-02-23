<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WatchAndLearnReadStatus extends Model
{
    protected $table = 'watch_and_learn_read_status';

    protected $fillable = [
        'user_id',
        'category',
        'read_watch',
    ];
}

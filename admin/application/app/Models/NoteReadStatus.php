<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NoteReadStatus extends Model
{
    protected $table = 'note_read_status';

    protected $fillable = [
        'user_id',
        'category',
        'read_note',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OsceQuesAns extends Model
{

    protected $table = 'osce_ques_ans';
    
    public $timestamps = false;
 
    protected $fillable = [
        'osce_id',
        'question',
        'answer',
    ];
}

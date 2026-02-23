<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Basic extends Model
{
    public function categories(){
        return $this->belongsTo(BasicCategory::class,'category','id');
    }
}

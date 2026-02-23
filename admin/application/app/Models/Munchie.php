<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Munchie extends Model
{
    public function categories(){
        return $this->belongsTo(MunchieCategory::class,'category','id');
    }
}

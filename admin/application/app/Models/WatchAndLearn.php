<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WatchAndLearn extends Model
{
    public function categories(){
        return $this->belongsTo(WatchCategory::class,'category','id');
    }
}

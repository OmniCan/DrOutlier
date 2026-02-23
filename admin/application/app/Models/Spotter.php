<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Spotter extends Model
{
    public function categories(){
        return $this->belongsTo(SpottersCategory::class,'category','id');
    }
}

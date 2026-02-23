<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    public function categories(){
        return $this->belongsTo(Category::class,'category','id');
    }
}

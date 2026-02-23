<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BasicCategory extends Model
{

    public function notes(){
        return $this->hasMany(Basic::class, 'category');
    }

    public function readnotes(){
        return $this->hasMany(BasicReadStatus::class, 'category');
    }


    
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MunchieCategory extends Model
{

    public function notes(){
        return $this->hasMany(Munchie::class, 'category');
    }

    public function readnotes(){
        return $this->hasMany(MunchieReadStatus::class, 'category');
    }


    
}

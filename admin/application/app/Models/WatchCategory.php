<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WatchCategory extends Model
{

    public function notes(){
        return $this->hasMany(WatchAndLearn::class, 'category');
    }

    public function readnotes(){
        return $this->hasMany(WatchAndLearnReadStatus::class, 'category');
    }


    
}

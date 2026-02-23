<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{

    public function notes(){
        return $this->hasMany(Blog::class, 'category');
    }

    public function readnotes(){
        return $this->hasMany(NoteReadStatus::class, 'category');
    }

    public function child(){
        return $this->hasMany(Category::class, 'parent_id');
    }



    
}

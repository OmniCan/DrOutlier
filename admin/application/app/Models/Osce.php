<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Osce extends Model
{
    protected $table = "osce";

    protected $fillable = ['title', 'category', 'image', 'content', 'status'];

    public function question(){
        return $this->hasMany(OsceQuesAns::class,'osce_id','id');
    }

    public function categories(){
        return $this->belongsTo(OsceCategory::class, 'category', 'id');
    }
}

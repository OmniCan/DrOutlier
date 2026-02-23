<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OsceCategory extends Model
{
    protected $table = 'osce_categories';

    protected $fillable = ['name', 'color', 'status', 'parent_id'];

    public function osces(){
        return $this->hasMany(Osce::class, 'category', 'id');
    }

    public function children(){
        return $this->hasMany(OsceCategory::class, 'parent_id', 'id')->where('status', 1);
    }

    public function parent(){
        return $this->belongsTo(OsceCategory::class, 'parent_id', 'id');
    }
}

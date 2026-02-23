<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewOsceCategory extends Model
{
    protected $table = 'new_osce_categories';

    protected $fillable = [
        'name',
        'parent_id',
        'color',
        'status',
        'is_premium'
    ];

    public function children()
    {
        return $this->hasMany(NewOsceCategory::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(NewOsceCategory::class, 'parent_id');
    }

    public function osce()
    {
        return $this->hasMany(NewOsce::class, 'category');
    }
}

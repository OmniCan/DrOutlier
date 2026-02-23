<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewSpottersCategory extends Model
{
    protected $table = 'new_spotters_categories';

    protected $fillable = [
        'name',
        'parent_id',
        'color',
        'status',
        'is_premium'
    ];

    public function children()
    {
        return $this->hasMany(NewSpottersCategory::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(NewSpottersCategory::class, 'parent_id');
    }

    public function spotters()
    {
        return $this->hasMany(NewSpotter::class, 'category');
    }
}

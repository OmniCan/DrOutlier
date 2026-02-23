<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PracticalEssentialsCategory extends Model
{
    protected $table = 'practical_essentials_categories';

    protected $fillable = [
        'parent_id',
        'name',
        'color',
        'status',
        'is_premium'
    ];

    public function parent()
    {
        return $this->belongsTo(PracticalEssentialsCategory::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(PracticalEssentialsCategory::class, 'parent_id');
    }

    public function practicalEssentials()
    {
        return $this->hasMany(PracticalEssentials::class, 'category', 'id');
    }
}

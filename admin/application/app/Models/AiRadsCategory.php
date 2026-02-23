<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiRadsCategory extends Model
{
    protected $table = 'ai_rads_categories';

    protected $fillable = [
        'parent_id',
        'name',
        'color',
        'status',
        'is_premium'
    ];

    public function parent()
    {
        return $this->belongsTo(AiRadsCategory::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(AiRadsCategory::class, 'parent_id');
    }

    public function aiRads()
    {
        return $this->hasMany(AiRads::class, 'category', 'id');
    }
}

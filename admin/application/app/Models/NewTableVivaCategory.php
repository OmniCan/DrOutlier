<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewTableVivaCategory extends Model
{
    protected $table = 'new_table_viva_categories';

    protected $fillable = [
        'name',
        'parent_id',
        'color',
        'status',
        'is_premium'
    ];

    public function children()
    {
        return $this->hasMany(NewTableVivaCategory::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(NewTableVivaCategory::class, 'parent_id');
    }

    public function items()
    {
        return $this->hasMany(NewTableViva::class, 'category');
    }
}

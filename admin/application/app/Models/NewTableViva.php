<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewTableViva extends Model
{
    protected $table = 'new_table_viva';

    protected $fillable = [
        'category',
        'title',
        'sort_order',
        'image',
        'description',
        'pdf_file',
        'is_premium'
    ];

    public function categories()
    {
        return $this->belongsTo(NewTableVivaCategory::class, 'category', 'id');
    }
}

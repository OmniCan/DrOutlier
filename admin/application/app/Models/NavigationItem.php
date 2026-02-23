<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NavigationItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'url',
        'icon',
        'module_id',
        'sort_order',
        'is_active',
        'show_in_navbar',
        'requires_auth',
        'type',
        'visibility_type'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'show_in_navbar' => 'boolean',
        'requires_auth' => 'boolean',
    ];

    // Relationship with Module
    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeNavbar($query)
    {
        return $query->where('show_in_navbar', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    // Get navigation items for frontend
    public static function getNavbarItems()
    {
        return self::with('module')
            ->active()
            ->navbar()
            ->ordered()
            ->get();
    }
}

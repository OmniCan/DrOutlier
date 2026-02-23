<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'discount_price',
        'duration_type',
        'duration_value',
        'razorpay_plan_id',
        'is_active',
        'is_featured',
        'sort_order',
        'features'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
        'duration_value' => 'integer',
        'features' => 'array',
    ];

    /**
     * Get the modules included in this plan.
     */
    public function modules()
    {
        return $this->belongsToMany(Module::class, 'plan_modules');
    }

    /**
     * Get the subscriptions for this plan.
     */
    public function subscriptions()
    {
        return $this->hasMany(UserSubscription::class);
    }

    /**
     * Get the users subscribed to this plan.
     */
    public function users()
    {
        return $this->hasManyThrough(User::class, UserSubscription::class, 'plan_id', 'id', 'id', 'user_id')
            ->where('user_subscriptions.status', 'active');
    }

    /**
     * Scope a query to only include active plans.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include featured plans.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to order by sort order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    /**
     * Get the effective price (discount price if available, otherwise regular price).
     */
    public function getEffectivePriceAttribute()
    {
        return $this->discount_price ?? $this->price;
    }

    /**
     * Get the duration in human readable format.
     */
    public function getDurationTextAttribute()
    {
        $value = $this->duration_value;
        $type = $this->duration_type;

        return $value . ' ' . ucfirst($type);
    }
}

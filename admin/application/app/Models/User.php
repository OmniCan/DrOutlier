<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{

    use HasApiTokens, SoftDeletes;
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */


    protected $softDelete = true;
    protected $dates = ['deleted_at'];

    protected $hidden = [
        'password', 'remember_token', 'HasApiTokens'
    ];

    protected $fillable = [
        'firstname', 'username', 'email', 'mobile', 'image', 'status', 'fcm_token', 'vidhan_sabha'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'address' => 'object',
        'kyc_data' => 'object',
        'ver_code_send_at' => 'datetime'
    ];


    public function loginLogs()
    {
        return $this->hasMany(UserLogin::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class)->orderBy('id','desc');
    }

    public function deposits()
    {
        return $this->hasMany(Deposit::class)->where('status','!=',0);
    }



    public function fullname(): Attribute {
        return new Attribute(
            get: fn() => $this->firstname || $this->lastname ? $this->firstname . ' ' . $this->lastname : '@'.$this->username,
        );
    }

    // SUBSCRIPTION RELATIONSHIPS
    public function subscriptions()
    {
        return $this->hasMany(UserSubscription::class);
    }

    public function activeSubscription()
    {
        return $this->hasOne(UserSubscription::class)
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->latest();
    }

    public function activeSubscriptions()
    {
        return $this->hasMany(UserSubscription::class)
            ->where('status', 'active')
            ->where('expires_at', '>', now());
    }

    public function hasActiveSubscription()
    {
        return $this->activeSubscriptions()->exists();
    }

    public function hasAccessToModule($moduleSlug)
    {
        // Check ALL active subscriptions, not just one
        $activeSubscriptions = $this->activeSubscriptions()->with('plan.modules')->get();

        if ($activeSubscriptions->isEmpty()) {
            return false;
        }

        // Check if any active subscription's plan includes this module
        foreach ($activeSubscriptions as $subscription) {
            if ($subscription->plan->modules()->where('slug', $moduleSlug)->exists()) {
                return true;
            }
        }

        return false;
    }

    public function getAccessibleModules()
    {
        // Get modules from ALL active subscriptions
        $activeSubscriptions = $this->activeSubscriptions()->with('plan.modules')->get();

        if ($activeSubscriptions->isEmpty()) {
            return collect([]);
        }

        // Merge all modules from all active subscriptions and remove duplicates
        $allModules = collect([]);
        foreach ($activeSubscriptions as $subscription) {
            $allModules = $allModules->merge($subscription->plan->modules);
        }

        return $allModules->unique('id');
    }

    // SCOPES
    public function scopeActive()
    {
        return $this->where('status', 1);
    }

    public function scopeBanned()
    {
        return $this->where('status', 0);
    }



    public function scopeWithBalance()
    {
        return $this->where('balance','>', 0);
    }

}

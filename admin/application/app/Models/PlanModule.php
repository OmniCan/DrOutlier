<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanModule extends Model
{
    protected $fillable = [
        'plan_id',
        'module_id'
    ];

    /**
     * Get the plan that owns the plan module.
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Get the module that owns the plan module.
     */
    public function module()
    {
        return $this->belongsTo(Module::class);
    }
}

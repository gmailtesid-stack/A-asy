<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'legal_entity', 'registration_number', 'plan_id', 
        'subscription_plan', 'max_products',
        'subscription_expires_at', 'currency', 'timezone', 'settings'
    ];

    protected $casts = [
        'settings' => 'array',
        'subscription_expires_at' => 'datetime',
    ];

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    public function isModuleEnabled(string $module): bool
    {
        // Check if module is in the plan features OR explicitly enabled in settings
        $planFeatures = $this->plan->features ?? [];
        $companySettings = $this->settings['enabled_modules'] ?? [];
        
        return in_array($module, $planFeatures) || in_array($module, $companySettings);
    }
}

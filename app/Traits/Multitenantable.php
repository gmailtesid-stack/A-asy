<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait Multitenantable
{
    /**
     * Boot the trait to apply global tenancy scopes.
     */
    protected static function bootMultitenantable()
    {
        // 1. GLOBAL SCOPE: Filtering data based on user hierarchy
        static::addGlobalScope('tenancy', function (Builder $builder) {
            if (Auth::check()) {
                $user = Auth::user();
                $table = $builder->getModel()->getTable();

                // Super Admin can see everything
                if ($user->role === 'super_admin') {
                    return;
                }

                // Filtering strategy based on role and available IDs
                if ($user->role === 'cashier' && !empty($user->outlet_id)) {
                    $builder->where($table . '.outlet_id', $user->outlet_id);
                } elseif ($user->role === 'manager' && !empty($user->branch_id)) {
                    $builder->where($table . '.branch_id', $user->branch_id);
                } else {
                    // Default to Company isolation
                    $builder->where($table . '.company_id', $user->company_id);
                }
            }
        });

        // 2. AUTO-FILL: Injecting tenancy IDs on creation
        static::creating(function ($model) {
            if (Auth::check()) {
                $user = Auth::user();
                if (empty($model->company_id)) {
                    $model->company_id = $user->company_id;
                }
                if (empty($model->branch_id)) {
                    $model->branch_id = $user->branch_id;
                }
                if (empty($model->outlet_id) && property_exists($model, 'outlet_id')) {
                    $model->outlet_id = $user->outlet_id;
                }
            }
        });

        // 3. SECURITY: Prevent Tenant Hopping on Update/Delete
        static::updating(function ($model) {
            if (Auth::check()) {
                $user = Auth::user();
                if ($user->role !== 'super_admin' && $model->getOriginal('company_id') != $user->company_id) {
                    throw new \Illuminate\Auth\Access\AuthorizationException('Unauthorized tenancy update.');
                }
                
                // Prevent changing company_id
                if ($model->isDirty('company_id') && $user->role !== 'super_admin') {
                    $model->company_id = $model->getOriginal('company_id');
                }
            }
        });

        static::deleting(function ($model) {
            if (Auth::check()) {
                $user = Auth::user();
                if ($user->role !== 'super_admin' && $model->getOriginal('company_id') != $user->company_id) {
                    throw new \Illuminate\Auth\Access\AuthorizationException('Unauthorized tenancy deletion.');
                }
            }
        });
    }
}

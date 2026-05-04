<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Traits\Multitenantable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, \App\Traits\Auditable, Multitenantable;

    protected $fillable = [
        'company_id', 'branch_id', 'outlet_id', 'name', 'email', 'password', 'role', 'is_active', 'photo', 'commission_rate', 'timezone', 'locale',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active'         => 'boolean',
    ];

    // ── RBAC Helpers (Optimized for Serverless Performance) ──────────
    public function roles()
    {
        return $this->morphToMany(Role::class, 'model', 'model_has_roles', 'model_id', 'role_id');
    }

    /**
     * Cache the roles collection to prevent multiple DB queries per request.
     */
    protected $loadedRoles = null;

    protected function getLoadedRoles()
    {
        if ($this->loadedRoles === null) {
            $this->loadedRoles = $this->roles()->with('permissions')->get();
        }
        return $this->loadedRoles;
    }

    public function hasRole($roleSlug): bool
    {
        return $this->getLoadedRoles()->contains('slug', $roleSlug);
    }

    public function hasPermission($permissionSlug): bool
    {
        return $this->getLoadedRoles()->flatMap(function ($role) {
            return $role->permissions;
        })->contains('slug', $permissionSlug);
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isSupervisor(): bool
    {
        return $this->hasRole('supervisor');
    }

    public function isOperator(): bool
    {
        return $this->hasRole('operator');
    }

    public function canAccessOutlet(int $outletId): bool
    {
        return $this->isSuperAdmin() || $this->outlet_id === $outletId;
    }

    // ── Relasi ───────────────────────────────────────────────────────
    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}

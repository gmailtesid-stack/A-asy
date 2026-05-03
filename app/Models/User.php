<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use Notifiable, SoftDeletes;

    protected $fillable = [
        'outlet_id', 'name', 'email', 'password', 'role', 'is_active',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active'         => 'boolean',
    ];

    // ── RBAC Helpers ─────────────────────────────────────────────────
    public function roles()
    {
        return $this->morphToMany(Role::class, 'model', 'model_has_roles', 'model_id', 'role_id');
    }

    public function hasRole($roleSlug): bool
    {
        return $this->roles()->where('slug', $roleSlug)->exists();
    }

    public function hasPermission($permissionSlug): bool
    {
        return $this->roles()->whereHas('permissions', function ($q) use ($permissionSlug) {
            $q->where('slug', $permissionSlug);
        })->exists();
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

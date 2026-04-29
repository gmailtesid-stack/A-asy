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

    // ── Role Helpers ─────────────────────────────────────────────────
    public function isSuperAdmin(): bool { return $this->role === 'super_admin'; }
    public function isManager(): bool    { return $this->role === 'manager'; }
    public function isCashier(): bool    { return $this->role === 'cashier'; }

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

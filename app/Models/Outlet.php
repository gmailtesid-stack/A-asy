<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Outlet extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'code', 'address', 'phone', 'city', 'is_active', 'latitude', 'longitude', 'photo'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ── Relasi ───────────────────────────────────────────────────────
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    // ── Scope ────────────────────────────────────────────────────────
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

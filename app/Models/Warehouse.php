<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Warehouse extends Model
{
    protected $fillable = ['outlet_id', 'name', 'address', 'latitude', 'longitude', 'photo', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
        'latitude'  => 'float',
        'longitude' => 'float',
    ];

    public function getPhotoUrlAttribute(): string
    {
        return $this->photo
            ? \Illuminate\Support\Facades\Storage::url($this->photo)
            : 'https://placehold.co/600x200/e0e7ff/6366f1?text=Gudang';
    }

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    public function locations(): HasMany
    {
        return $this->hasMany(Location::class);
    }

    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }
}

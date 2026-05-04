<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Warehouse extends Model
{
    use \App\Traits\Multitenantable, \App\Traits\Auditable;

    protected $fillable = ['company_id', 'branch_id', 'outlet_id', 'name', 'address', 'latitude', 'longitude', 'photo', 'type'];

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

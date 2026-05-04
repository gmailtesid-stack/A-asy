<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WarehouseBin extends Model
{
    protected $fillable = ['zone_id', 'rack', 'level', 'bin', 'full_code'];

    public function zone(): BelongsTo
    {
        return $this->belongsTo(WarehouseZone::class);
    }

    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class, 'bin_id');
    }
}

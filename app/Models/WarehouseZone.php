<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WarehouseZone extends Model
{
    protected $fillable = ['warehouse_id', 'code', 'name'];

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function bins(): HasMany
    {
        return $this->hasMany(WarehouseBin::class, 'zone_id');
    }
}

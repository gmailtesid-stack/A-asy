<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendingStockAdjustment extends Model
{
    protected $fillable = ['inventory_id', 'quantity_change', 'reference_type', 'reference_id'];

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }
}

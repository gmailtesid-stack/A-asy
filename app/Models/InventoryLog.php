<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryLog extends Model
{
    protected $fillable = [
        'inventory_id', 'user_id', 'type',
        'quantity_before', 'quantity_change', 'quantity_after',
        'reference', 'notes', 'cost_price', 'remaining_quantity',
    ];

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

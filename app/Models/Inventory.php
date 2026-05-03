<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $fillable = [
        'outlet_id', 'warehouse_id', 'product_id', 'location_id', 'quantity', 'min_quantity',
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function logs()
    {
        return $this->hasMany(InventoryLog::class);
    }

    // Cek apakah stok di bawah minimum
    public function isLowStock(): bool
    {
        return $this->quantity < $this->min_quantity;
    }
}

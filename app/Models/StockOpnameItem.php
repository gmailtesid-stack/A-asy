<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockOpnameItem extends Model
{
    protected $fillable = ['stock_opname_id', 'product_id', 'recorded_quantity', 'physical_quantity', 'adjustment_quantity'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

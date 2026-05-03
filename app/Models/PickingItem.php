<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PickingItem extends Model
{
    protected $fillable = ['picking_id', 'product_id', 'quantity_requested', 'quantity_found', 'status'];

    public function picking(): BelongsTo
    {
        return $this->belongsTo(Picking::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}

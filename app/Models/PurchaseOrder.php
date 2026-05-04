<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Traits\Auditable;

class PurchaseOrder extends Model
{
    use Auditable;
    protected $fillable = [
        'supplier_id', 'user_id', 'warehouse_id', 'po_number', 
        'status', 'total_amount', 'notes', 'approved_by', 'approved_at',
        'freight_cost', 'insurance_cost', 'estimated_landed_cost'
    ];

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function grns(): HasMany
    {
        return $this->hasMany(Grn::class);
    }
}

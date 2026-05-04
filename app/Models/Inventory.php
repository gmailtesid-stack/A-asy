<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;
use App\Traits\HasUlidSync;
use App\Traits\Multitenantable;

class Inventory extends Model
{
    use Auditable, HasUlidSync, Multitenantable;

    protected $fillable = [
        'ulid', 'company_id', 'branch_id',
        'outlet_id', 'warehouse_id', 'product_id', 'location_id', 'bin_id',
        'quantity', 'min_quantity', 'reserved_quantity', 'reorder_point',
        'is_frozen', 'version', 'synced_at'
    ];

    protected $casts = [
        'is_frozen' => 'boolean',
        'synced_at' => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────────────
    public function outlet()    { return $this->belongsTo(Outlet::class); }
    public function warehouse() { return $this->belongsTo(Warehouse::class); }
    public function product()   { return $this->belongsTo(Product::class); }
    public function location()  { return $this->belongsTo(Location::class); }
    public function logs()      { return $this->hasMany(InventoryLog::class); }
    public function bin()       { return $this->belongsTo(WarehouseBin::class, 'bin_id'); }

    // ── Stock State Helpers ────────────────────────────────────────

    /** Stok fisik di rak */
    public function physicalStock(): int
    {
        return $this->quantity;
    }

    /** Stok tersedia = Fisik - Dipesan (Virtual) */
    public function availableStock(): int
    {
        return max(0, $this->quantity - $this->reserved_quantity);
    }

    /** Reserve stock for an order (OMS booking) */
    public function reserve(int $qty): bool
    {
        if ($this->availableStock() < $qty) return false;
        $this->increment('reserved_quantity', $qty);
        return true;
    }

    /** Release reserved stock (order cancelled or shipped) */
    public function releaseReservation(int $qty): void
    {
        $this->decrement('reserved_quantity', min($qty, $this->reserved_quantity));
    }

    /** Stok di bawah minimum? */
    public function isLowStock(): bool
    {
        return $this->quantity < $this->min_quantity;
    }

    /** Stok menyentuh reorder point? */
    public function needsReorder(): bool
    {
        return $this->reorder_point > 0 && $this->quantity <= $this->reorder_point;
    }
}

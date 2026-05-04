<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\Auditable;
use App\Traits\HasUlidSync;
use App\Traits\Multitenantable;

class Product extends Model
{
    use SoftDeletes, Auditable, HasUlidSync, Multitenantable;
    
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            $company = \App\Models\Company::find($product->company_id);
            if ($company && $company->subscription_plan === 'basic') {
                $count = Product::withoutGlobalScopes()->where('company_id', $company->id)->count();
                if ($count >= ($company->max_products ?? 100)) {
                    throw new \Exception('Subscription limit reached: Maximum products for Basic plan.');
                }
            }
        });
    }

    protected $fillable = [
        'ulid', 'company_id', 'branch_id',
        'category_id', 'brand_id', 'supplier_id',
        'name', 'sku', 'csku', 'barcode', 'description',
        'status', 'type', 'is_active',
        'price', 'cost_price', 'wholesale_price', 'member_price',
        'base_uom', 'purchase_uom', 'conversion_rate', 'unit',
        'weight', 'dimensions', 'image', 'image_url'
    ];

    protected $casts = [
        'price'           => 'decimal:2',
        'cost_price'      => 'decimal:2',
        'wholesale_price' => 'decimal:2',
        'member_price'    => 'decimal:2',
        'is_active'       => 'boolean',
    ];

    public function scopeLive($query)
    {
        return $query->where('status', 'live');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeUnderReview($query)
    {
        return $query->where('status', 'under_review');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }

    public function poItems()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function grnItems()
    {
        return $this->hasMany(GrnItem::class);
    }

    public function soItems()
    {
        return $this->hasMany(SalesOrderItem::class);
    }

    public function pickingItems()
    {
        return $this->hasMany(PickingItem::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Helper: URL gambar (Cloudinary atau placeholder)
    public function getImageUrlAttribute(): string
    {
        return $this->image
            ? $this->image
            : 'https://placehold.co/200x200?text=' . urlencode($this->name);
    }
}

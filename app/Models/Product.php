<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id', 'name', 'sku', 'description',
        'price', 'cost_price', 'unit', 'image', 'is_active',
    ];

    protected $casts = [
        'price'      => 'decimal:2',
        'cost_price' => 'decimal:2',
        'is_active'  => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class);
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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class ReturnItem extends Model
{
    use Auditable;

    protected $fillable = [
        'return_id', 'product_id', 'quantity', 'refund_amount', 'condition'
    ];

    public function returnModel() { return $this->belongsTo(ReturnModel::class, 'return_id'); }
    public function product()     { return $this->belongsTo(Product::class); }
}

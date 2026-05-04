<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\Auditable;

class StockOpname extends Model
{
    use Auditable;
    protected $fillable = ['warehouse_id', 'user_id', 'approved_by', 'opname_number', 'status', 'notes'];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(StockOpnameItem::class);
    }
}

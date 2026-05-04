<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\Auditable;
use App\Traits\HasUlidSync;
use App\Traits\Multitenantable;

class Transaction extends Model
{
    use Auditable, HasUlidSync, Multitenantable;
    protected $fillable = [
        'ulid', 'company_id', 'branch_id', 'outlet_id', 'user_id', 'customer_id', 'invoice_number',
        'subtotal', 'discount', 'tax', 'total',
        'cash_amount', 'change_amount',
        'payment_method', 'status', 'notes',
        'currency', 'exchange_rate', 'tax_included', 'tax_rate', 'tax_amount'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    protected $casts = [
        'subtotal'      => 'decimal:2',
        'discount'      => 'decimal:2',
        'tax'           => 'decimal:2',
        'total'         => 'decimal:2',
        'cash_amount'   => 'decimal:2',
        'change_amount' => 'decimal:2',
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function cashier()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function details()
    {
        return $this->hasMany(TransactionDetail::class);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }
}

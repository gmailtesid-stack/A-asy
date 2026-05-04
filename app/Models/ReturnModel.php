<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class ReturnModel extends Model
{
    use Auditable;
    
    protected $table = 'returns';
    
    protected $fillable = [
        'return_number', 'transaction_id', 'customer_id', 'outlet_id', 
        'processed_by', 'refund_method', 'total_refund', 'reason'
    ];

    public function transaction() { return $this->belongsTo(Transaction::class); }
    public function customer()    { return $this->belongsTo(Customer::class); }
    public function outlet()      { return $this->belongsTo(Outlet::class); }
    public function processor()   { return $this->belongsTo(User::class, 'processed_by'); }
    public function items()       { return $this->hasMany(ReturnItem::class, 'return_id'); }
}

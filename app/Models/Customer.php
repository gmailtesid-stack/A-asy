<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\HasUlidSync;
use App\Traits\Multitenantable;

class Customer extends Model
{
    use HasUlidSync, Multitenantable;

    protected $fillable = [
        'ulid', 'company_id', 'branch_id',
        'name',
        'email',
        'phone',
        'address',
        'points',
        'store_credit',
        'tier'
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }
}

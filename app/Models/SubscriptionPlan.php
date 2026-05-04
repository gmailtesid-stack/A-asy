<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    protected $fillable = ['name', 'price', 'features', 'max_outlets', 'max_users'];
    protected $casts = ['features' => 'array'];
}

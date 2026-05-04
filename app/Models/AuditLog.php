<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id', 'action', 'model_type', 'model_id', 'description', 
        'old_values', 'new_values', 'ip_address', 'user_agent', 'url'
    ];
}

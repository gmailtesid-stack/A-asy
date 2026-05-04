<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\Auditable;

class Employee extends Model
{
    use Auditable;

    protected $fillable = ['user_id', 'employee_id', 'name', 'position', 'salary', 'joined_at', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}

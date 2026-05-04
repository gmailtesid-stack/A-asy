<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = ['employee_id', 'date', 'clock_in', 'clock_out', 'status', 'notes'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}

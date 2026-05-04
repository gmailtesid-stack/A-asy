<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\Auditable;

class JournalEntry extends Model
{
    use Auditable;

    protected $fillable = ['entry_date', 'reference', 'description', 'user_id'];

    public function lines()
    {
        return $this->hasMany(JournalLine::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

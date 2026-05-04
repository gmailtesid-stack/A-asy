<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Approval extends Model
{
    protected $fillable = [
        'approvable_type', 'approvable_id',
        'requested_by', 'approved_by', 'status',
        'notes', 'amount_before', 'amount_after', 'decided_at',
    ];

    protected $casts = [
        'decided_at'   => 'datetime',
        'amount_before' => 'decimal:2',
        'amount_after'  => 'decimal:2',
    ];

    // ── Polymorphic Relationship ────────────────────────────────────
    public function approvable()  { return $this->morphTo(); }
    public function requester()   { return $this->belongsTo(User::class, 'requested_by'); }
    public function approver()    { return $this->belongsTo(User::class, 'approved_by'); }

    // ── Scopes ─────────────────────────────────────────────────────
    public function scopePending($q)  { return $q->where('status', 'pending'); }
    public function scopeApproved($q) { return $q->where('status', 'approved'); }

    // ── Helpers ────────────────────────────────────────────────────
    public function approve(User $manager, string $notes = null): void
    {
        $this->update([
            'approved_by' => $manager->id,
            'status'      => 'approved',
            'notes'       => $notes,
            'decided_at'  => now(),
        ]);
    }

    public function reject(User $manager, string $notes): void
    {
        $this->update([
            'approved_by' => $manager->id,
            'status'      => 'rejected',
            'notes'       => $notes,
            'decided_at'  => now(),
        ]);
    }
}

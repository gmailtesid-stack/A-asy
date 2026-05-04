<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'outlet_id', 'user_id', 'approved_by', 'category',
        'description', 'amount', 'expense_date', 'status',
        'receipt_url', 'notes', 'journal_entry_id',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount'       => 'decimal:2',
    ];

    // ── Relationships ──────────────────────────────────────────────
    public function outlet()     { return $this->belongsTo(Outlet::class); }
    public function user()       { return $this->belongsTo(User::class); }
    public function approver()   { return $this->belongsTo(User::class, 'approved_by'); }
    public function journal()    { return $this->belongsTo(JournalEntry::class, 'journal_entry_id'); }

    // ── Scopes ─────────────────────────────────────────────────────
    public function scopePending($q)  { return $q->where('status', 'pending'); }
    public function scopeApproved($q) { return $q->where('status', 'approved'); }

    public function scopeByCategory($q, string $cat) { return $q->where('category', $cat); }
    public function scopeByPeriod($q, $from, $to)
    {
        return $q->whereBetween('expense_date', [$from, $to]);
    }
}

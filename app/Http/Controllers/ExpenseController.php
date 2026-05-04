<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Approval;
use App\Models\JournalEntry;
use App\Models\JournalLine;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    /**
     * List all expenses for the current outlet.
     */
    public function index(Request $request)
    {
        $outletId = auth()->user()->outlet_id;

        $expenses = Expense::with(['user', 'approver'])
            ->when($outletId, fn($q) => $q->where('outlet_id', $outletId))
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->when($request->category, fn($q, $c) => $q->where('category', $c))
            ->when($request->from, fn($q, $f) => $q->where('expense_date', '>=', $f))
            ->when($request->to, fn($q, $t) => $q->where('expense_date', '<=', $t))
            ->latest('expense_date')
            ->paginate(20);

        $categories = ['rent', 'utilities', 'salary', 'packaging', 'marketing', 'other'];
        $summary = Expense::where('outlet_id', $outletId)
            ->where('status', 'approved')
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->pluck('total', 'category');

        return view('finance.expenses.index', compact('expenses', 'categories', 'summary'));
    }

    /**
     * Store a new expense — auto-creates an Approval record for manager sign-off.
     */
    public function store(Request $request)
    {
        $request->validate([
            'category'     => 'required|in:rent,utilities,salary,packaging,marketing,other',
            'description'  => 'required|string|max:255',
            'amount'       => 'required|numeric|min:1',
            'expense_date' => 'required|date',
            'notes'        => 'nullable|string|max:1000',
            'receipt'      => 'nullable|image|max:5120',
        ]);

        return DB::transaction(function () use ($request) {
            $receiptUrl = null;
            if ($request->hasFile('receipt')) {
                $receiptUrl = app(\App\Services\CloudinaryService::class)
                    ->upload($request->file('receipt'), 'expenses');
            }

            $expense = Expense::create([
                'outlet_id'    => auth()->user()->outlet_id,
                'user_id'      => auth()->id(),
                'category'     => $request->category,
                'description'  => $request->description,
                'amount'       => $request->amount,
                'expense_date' => $request->expense_date,
                'status'       => 'pending',
                'receipt_url'  => $receiptUrl,
                'notes'        => $request->notes,
            ]);

            // Create approval request
            Approval::create([
                'approvable_type' => Expense::class,
                'approvable_id'   => $expense->id,
                'requested_by'    => auth()->id(),
                'status'          => 'pending',
                'amount_after'    => $expense->amount,
            ]);

            return back()->with('success', "Pengeluaran \"{$expense->description}\" diajukan, menunggu persetujuan manajer.");
        });
    }

    /**
     * Manager approves an expense — creates journal entry automatically.
     */
    public function approve(Expense $expense)
    {
        $this->authorize('manage', auth()->user());

        DB::transaction(function () use ($expense) {
            $expense->update(['status' => 'approved', 'approved_by' => auth()->id()]);

            // Update the linked approval record
            Approval::where('approvable_type', Expense::class)
                ->where('approvable_id', $expense->id)
                ->pending()
                ->first()
                ?->approve(auth()->user());

            // Auto-journal: Debit Expense Account, Credit Cash/Bank
            $expenseAccount = Account::where('code', '5100')->first(); // OPEX
            $cashAccount    = Account::where('code', '1100')->first(); // Kas

            if ($expenseAccount && $cashAccount) {
                $journal = JournalEntry::create([
                    'reference'   => 'EXP-' . $expense->id,
                    'description' => "Biaya {$expense->category}: {$expense->description}",
                    'date'        => $expense->expense_date,
                    'type'        => 'expense',
                ]);

                JournalLine::create(['journal_entry_id' => $journal->id, 'account_id' => $expenseAccount->id, 'debit' => $expense->amount, 'credit' => 0]);
                JournalLine::create(['journal_entry_id' => $journal->id, 'account_id' => $cashAccount->id,    'debit' => 0, 'credit' => $expense->amount]);

                $expense->update(['journal_entry_id' => $journal->id]);
            }
        });

        return back()->with('success', 'Pengeluaran disetujui dan dicatat ke jurnal keuangan.');
    }

    /**
     * Manager rejects an expense.
     */
    public function reject(Request $request, Expense $expense)
    {
        $request->validate(['notes' => 'required|string|max:500']);
        $expense->update(['status' => 'rejected']);

        Approval::where('approvable_type', Expense::class)
            ->where('approvable_id', $expense->id)
            ->pending()
            ->first()
            ?->reject(auth()->user(), $request->notes);

        return back()->with('success', 'Pengeluaran ditolak.');
    }
}

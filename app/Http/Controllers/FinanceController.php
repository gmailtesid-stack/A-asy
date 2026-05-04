<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\JournalLine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    public function index()
    {
        // 1. Get Balances for Dashboard
        $cashBalance = $this->getAccountBalance('1101') + $this->getAccountBalance('1102');
        $totalRevenue = $this->getAccountBalance('4101', true); // Credit balance
        $totalExpense = $this->getAccountBalance('5101', false); // Debit balance
        
        $recentJournals = JournalEntry::with('lines.account')
            ->latest()
            ->limit(5)
            ->get();

        return view('finance.index', compact('cashBalance', 'totalRevenue', 'totalExpense', 'recentJournals'));
    }

    public function journals()
    {
        $journals = JournalEntry::with(['lines.account', 'user'])
            ->latest()
            ->paginate(20);

        return view('finance.journals', compact('journals'));
    }

    public function accounts()
    {
        $accounts = Account::withSum('journalLines as total_debit', 'debit')
            ->withSum('journalLines as total_credit', 'credit')
            ->orderBy('code')
            ->get();

        return view('finance.accounts', compact('accounts'));
    }

    public function reportProfitLoss(Request $request)
    {
        $startDate = $request->start_date ?? now()->startOfMonth()->format('Y-m-d');
        $endDate = $request->end_date ?? now()->endOfMonth()->format('Y-m-d');

        $revenues = Account::where('type', 'revenue')
            ->withSum(['journalLines as balance' => function($q) use ($startDate, $endDate) {
                $q->whereHas('entry', fn($eq) => $eq->whereBetween('entry_date', [$startDate, $endDate]));
            }], DB::raw('credit - debit'))
            ->get();

        $expenses = Account::where('type', 'expense')
            ->withSum(['journalLines as balance' => function($q) use ($startDate, $endDate) {
                $q->whereHas('entry', fn($eq) => $eq->whereBetween('entry_date', [$startDate, $endDate]));
            }], DB::raw('debit - credit'))
            ->get();

        $totalRevenue = $revenues->sum('balance');
        $totalExpense = $expenses->sum('balance');
        $netProfit = $totalRevenue - $totalExpense;

        return view('finance.reports.profit_loss', compact('revenues', 'expenses', 'totalRevenue', 'totalExpense', 'netProfit', 'startDate', 'endDate'));
    }

    private function getAccountBalance(string $code, bool $isCredit = false): float
    {
        $account = Account::where('code', $code)->first();
        if (!$account) return 0;

        $sum = JournalLine::where('account_id', $account->id)
            ->select(DB::raw('SUM(debit) as total_debit, SUM(credit) as total_credit'))
            ->first();

        if ($isCredit) {
            return ($sum->total_credit ?? 0) - ($sum->total_debit ?? 0);
        }
        return ($sum->total_debit ?? 0) - ($sum->total_credit ?? 0);
    }
}

<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\Expense;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BusinessIntelligenceService
{
    /**
     * Dead Stock Analysis: Products with no sales in the last N days.
     */
    public function getDeadStock(int $days = 90, ?int $outletId = null): \Illuminate\Support\Collection
    {
        $cutoff = Carbon::now()->subDays($days);

        return Inventory::with('product.category')
            ->when($outletId, fn($q) => $q->where('outlet_id', $outletId))
            ->where('quantity', '>', 0)
            ->whereDoesntHave('product', function ($q) use ($cutoff, $outletId) {
                $q->whereHas('transactionDetails', function ($td) use ($cutoff, $outletId) {
                    $td->whereHas('transaction', function ($t) use ($cutoff, $outletId) {
                        $t->where('created_at', '>=', $cutoff)
                          ->when($outletId, fn($x) => $x->where('outlet_id', $outletId));
                    });
                });
            })
            ->get()
            ->map(function ($inv) {
                return [
                    'product_id'   => $inv->product_id,
                    'product_name' => $inv->product->name ?? '-',
                    'category'     => $inv->product->category->name ?? '-',
                    'quantity'     => $inv->quantity,
                    'stock_value'  => $inv->quantity * ($inv->product->cost_price ?? 0),
                    'last_updated' => $inv->updated_at->diffForHumans(),
                ];
            })
            ->sortByDesc('stock_value');
    }

    /**
     * Channel Profitability: Revenue and margin comparison across channels.
     */
    public function getChannelProfitability(?int $outletId = null, string $period = 'month'): array
    {
        $from = match ($period) {
            'week'  => Carbon::now()->startOfWeek(),
            'month' => Carbon::now()->startOfMonth(),
            'year'  => Carbon::now()->startOfYear(),
            default => Carbon::now()->startOfMonth(),
        };

        // POS Revenue
        $posRevenue = Transaction::where('status', 'completed')
            ->when($outletId, fn($q) => $q->where('outlet_id', $outletId))
            ->where('created_at', '>=', $from)
            ->selectRaw('SUM(total) as revenue, SUM(discount) as total_discount')
            ->first();

        $posCOGS = DB::table('transaction_details')
            ->join('transactions', 'transactions.id', '=', 'transaction_details.transaction_id')
            ->where('transactions.status', 'completed')
            ->when($outletId, fn($q) => $q->where('transactions.outlet_id', $outletId))
            ->where('transactions.created_at', '>=', $from)
            ->selectRaw('SUM(cost_price * quantity) as cogs')
            ->value('cogs') ?? 0;

        // OMS/Marketplace Revenue (from Sales Orders with invoice)
        $omsRevenue = DB::table('sales_orders')
            ->where('status', 'shipped')
            ->where('created_at', '>=', $from)
            ->selectRaw('SUM(total_amount) as revenue')
            ->value('revenue') ?? 0;

        // Expenses for the period
        $totalExpenses = Expense::where('status', 'approved')
            ->when($outletId, fn($q) => $q->where('outlet_id', $outletId))
            ->where('expense_date', '>=', $from)
            ->sum('amount');

        $posGrossProfit = ($posRevenue->revenue ?? 0) - $posCOGS;
        $netProfit = $posGrossProfit + $omsRevenue - $totalExpenses;

        return [
            'period' => $period,
            'pos' => [
                'revenue'      => $posRevenue->revenue ?? 0,
                'cogs'         => $posCOGS,
                'discount'     => $posRevenue->total_discount ?? 0,
                'gross_profit' => $posGrossProfit,
                'margin_pct'   => ($posRevenue->revenue ?? 0) > 0
                    ? round(($posGrossProfit / $posRevenue->revenue) * 100, 2)
                    : 0,
            ],
            'oms' => [
                'revenue' => $omsRevenue,
            ],
            'expenses'   => $totalExpenses,
            'net_profit' => $netProfit,
        ];
    }

    /**
     * Reorder Alert: Products that have hit their reorder point.
     */
    public function getReorderAlerts(?int $outletId = null): \Illuminate\Support\Collection
    {
        return Inventory::with(['product', 'outlet'])
            ->whereColumn('quantity', '<=', 'reorder_point')
            ->where('reorder_point', '>', 0)
            ->when($outletId, fn($q) => $q->where('outlet_id', $outletId))
            ->get()
            ->map(fn($inv) => [
                'product_id'    => $inv->product_id,
                'product_name'  => $inv->product->name ?? '-',
                'outlet'        => $inv->outlet->name ?? '-',
                'current_stock' => $inv->quantity,
                'reorder_point' => $inv->reorder_point,
                'deficit'       => $inv->reorder_point - $inv->quantity,
            ]);
    }

    /**
     * Cash Flow Projection: Accounts Receivable (OMS) vs Accounts Payable (Purchasing).
     */
    public function getCashFlowProjection(?int $outletId = null): array
    {
        // Uang yang akan masuk: Sales Order (OMS) status 'processing' atau 'shipped' (belum paid)
        $accountsReceivable = DB::table('sales_orders')
            ->whereIn('status', ['processing', 'shipped']) // Assuming paid happens later
            ->selectRaw('SUM(total_amount) as total')
            ->value('total') ?? 0;

        // Uang yang akan keluar: Purchase Order (Inbound) status 'approved' atau 'shipped' (belum diterima/dibayar lunas)
        $accountsPayable = DB::table('purchase_orders')
            ->whereIn('status', ['approved', 'shipped'])
            ->when($outletId, fn($q) => $q->where('outlet_id', $outletId))
            ->selectRaw('SUM(total_amount) as total')
            ->value('total') ?? 0;

        // Saldo Kas Saat Ini (Kas/Bank)
        $currentCash = DB::table('journal_lines')
            ->join('accounts', 'accounts.id', '=', 'journal_lines.account_id')
            ->where('accounts.code', 'like', '11%') // Akun Kas & Bank
            ->selectRaw('SUM(debit) - SUM(credit) as balance')
            ->value('balance') ?? 0;

        return [
            'current_cash'        => $currentCash,
            'accounts_receivable' => $accountsReceivable,
            'accounts_payable'    => $accountsPayable,
            'projected_cash'      => $currentCash + $accountsReceivable - $accountsPayable,
            'health_status'       => ($currentCash + $accountsReceivable - $accountsPayable) > 0 ? 'healthy' : 'critical',
        ];
    }
}

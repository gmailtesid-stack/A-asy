<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        $user     = auth()->user();
        $outletId = $user->isSuperAdmin() ? null : $user->outlet_id;

        return view('reports.dashboard', [
            'dailyRevenue'  => $this->getDailyRevenue($outletId, 30),
            'outletCompare' => $this->getOutletComparison($outletId),
            'topProducts'   => $this->getTopSellingProducts($outletId, 10),
            'summaryStats'  => $this->getSummaryStats($outletId),
        ]);
    }

    public function liveStats()
    {
        $user     = auth()->user();
        $outletId = $user->isSuperAdmin() ? null : $user->outlet_id;

        return response()->json([
            'daily_revenue' => $this->getDailyRevenue($outletId, 30),
            'top_products'  => $this->getTopSellingProducts($outletId, 5),
            'summary'       => $this->getSummaryStats($outletId),
        ]);
    }

    private function getDailyRevenue(?int $outletId, int $days = 30): array
    {
        $query = DB::table('transactions')
            ->selectRaw('DATE(created_at) as date, SUM(total) as revenue, COUNT(*) as transactions')
            ->where('status', 'completed')
            ->where('created_at', '>=', now()->subDays($days));

        if ($outletId) $query->where('outlet_id', $outletId);

        $results = $query->groupByRaw('DATE(created_at)')->orderBy('date')->get()->keyBy('date');

        $labels = $revenues = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date       = now()->subDays($i)->format('Y-m-d');
            $labels[]   = now()->subDays($i)->format('d M');
            $revenues[] = (float) ($results[$date]->revenue ?? 0);
        }

        return compact('labels', 'revenues');
    }

    private function getOutletComparison(?int $outletId): array
    {
        $query = DB::table('transactions as t')
            ->join('outlets as o', 't.outlet_id', '=', 'o.id')
            ->selectRaw('o.name as outlet_name, o.code, SUM(t.total) as revenue, COUNT(t.id) as total_trx')
            ->where('t.status', 'completed')
            ->whereMonth('t.created_at', now()->month)
            ->whereYear('t.created_at', now()->year);

        if ($outletId) $query->where('t.outlet_id', $outletId);

        return $query->groupBy('o.id', 'o.name', 'o.code')->orderByDesc('revenue')->get()->toArray();
    }

    private function getTopSellingProducts(?int $outletId, int $limit = 10): array
    {
        $query = DB::table('transaction_details as td')
            ->join('transactions as t', 'td.transaction_id', '=', 't.id')
            ->join('products as p', 'td.product_id', '=', 'p.id')
            ->join('categories as c', 'p.category_id', '=', 'c.id')
            ->selectRaw('p.id, td.product_name, c.name as category, SUM(td.quantity) as total_sold, SUM(td.subtotal) as total_revenue')
            ->where('t.status', 'completed');

        if ($outletId) $query->where('t.outlet_id', $outletId);

        return $query->groupBy('p.id', 'td.product_name', 'c.name')
            ->orderByDesc('total_sold')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    private function getSummaryStats(?int $outletId): array
    {
        $base = DB::table('transactions')->where('status', 'completed');
        if ($outletId) $base->where('outlet_id', $outletId);

        $today = (clone $base)->whereDate('created_at', today());
        $month = (clone $base)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);

        return [
            'today_revenue' => $today->sum('total'),
            'today_trx'     => $today->count(),
            'month_revenue' => $month->sum('total'),
            'month_trx'     => $month->count(),
        ];
    }
}

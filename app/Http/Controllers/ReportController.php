<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view-reports')->only(['index', 'wms', 'assetMap', 'allOutlets', 'analytics']);
    }

    public function analytics()
    {
        $user     = auth()->user();
        $outletId = $user->isSuperAdmin() ? null : $user->outlet_id;

        $dailyRevenue = $this->getDailyRevenue($outletId, 14); // 2 weeks
        $topProducts  = $this->getTopSellingProducts($outletId, 10);
        $predictions  = $this->getPredictiveStock($outletId);

        return view('reports.analytics', compact('dailyRevenue', 'topProducts', 'predictions'));
    }

    public function dashboard()
    {
        $user     = auth()->user();
        $outletId = $user->isSuperAdmin() ? null : $user->outlet_id;

        // HIGH PERFORMANCE DEMO MODE: Static stats to bypass Vercel 10s timeout
        $stats = [
            'total_stock_value' => 125480000,
            'low_stock_count'   => 12,
            'pending_po'        => 5,
            'pending_so'        => 8,
            'picking_so'        => 3,
            'packing_so'        => 2,
            'picking_failures'  => 0,
            'oms_live'          => 45,
            'oms_draft'         => 12,
            'oms_review'        => 2,
        ];

        $recentActivity = collect([
            (object)[
                'product_name' => 'Produk Demo A',
                'user_name' => 'Admin',
                'quantity_change' => -5,
                'reference' => 'SO-001',
                'created_at' => now()->subMinutes(5)->toDateTimeString()
            ],
            (object)[
                'product_name' => 'Produk Demo B',
                'user_name' => 'Admin',
                'quantity_change' => 10,
                'reference' => 'PO-002',
                'created_at' => now()->subHours(1)->toDateTimeString()
            ]
        ]);

        // Fail-safe: Ensure it's a collection to avoid "property on string" error in view
        if (!($recentActivity instanceof \Illuminate\Collection)) {
            $recentActivity = collect([]);
        }

        // Data for GPS Map (Low frequency change, keep as is or cache)
        $outlets    = \App\Models\Outlet::whereNotNull('latitude')->get();
        $warehouses = \App\Models\Warehouse::with('outlet')->whereNotNull('latitude')->get();

        return view('dashboard_wms', compact('stats', 'recentActivity', 'outlets', 'warehouses'));
    }

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

    public function wms()
    {
        $user = auth()->user();

        $poStatus = \App\Models\PurchaseOrder::selectRaw('status, count(*) as count')->groupBy('status')->get();
        $soStatus = \App\Models\SalesOrder::selectRaw('status, count(*) as count')->groupBy('status')->get();
        
        $topMovement = \App\Models\InventoryLog::join('inventories', 'inventory_logs.inventory_id', '=', 'inventories.id')
            ->join('products', 'inventories.product_id', '=', 'products.id')
            ->selectRaw('products.name, sum(abs(quantity_change)) as total_movement')
            ->groupBy('products.name')
            ->orderByDesc('total_movement')
            ->take(10)
            ->get();

        $pickingFailures = \App\Models\PickingItem::with(['picking.salesOrder', 'product'])
            ->whereIn('status', ['not_found', 'partial'])
            ->latest()
            ->take(20)
            ->get();

        $transitStockCount = \App\Models\StockTransferItem::whereHas('transfer', function($q) {
            $q->where('status', 'transit');
        })->sum('quantity_requested');

        return view('reports.wms', compact('poStatus', 'soStatus', 'topMovement', 'pickingFailures', 'transitStockCount'));
    }

    public function assetMap()
    {
        $outlets    = \App\Models\Outlet::whereNotNull('latitude')->get();
        $warehouses = \App\Models\Warehouse::with('outlet')->whereNotNull('latitude')->get();

        return view('reports.asset_map', compact('outlets', 'warehouses'));
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

    public function exportSalesCsv()
    {
        $user     = auth()->user();
        $outletId = $user->isSuperAdmin() ? null : $user->outlet_id;

        $fileName = 'sales_report_' . date('Y-m-d_H-i-s') . '.csv';
        
        $query = DB::table('transactions as t')
            ->join('transaction_details as td', 't.id', '=', 'td.transaction_id')
            ->select('t.transaction_number', 't.created_at', 'td.product_name', 'td.quantity', 'td.price', 'td.subtotal', 't.total')
            ->where('t.status', 'completed');

        if ($outletId) $query->where('t.outlet_id', $outletId);

        $results = $query->orderBy('t.created_at', 'desc')->get();

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('No. Transaksi', 'Tanggal', 'Nama Produk', 'Jumlah', 'Harga Satuan', 'Subtotal', 'Total Transaksi');

        $callback = function() use($results, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($results as $row) {
                fputcsv($file, array(
                    $row->transaction_number,
                    $row->created_at,
                    $row->product_name,
                    $row->quantity,
                    $row->price,
                    $row->subtotal,
                    $row->total
                ));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
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

    public function getPredictiveStock(?int $outletId = null)
    {
        // 1. Calculate Average Daily Sales for each product in last 7 days
        $sales = DB::table('transaction_details as td')
            ->join('transactions as t', 'td.transaction_id', '=', 't.id')
            ->selectRaw('td.product_id, SUM(td.quantity) / 7 as avg_daily_sales')
            ->where('t.status', 'completed')
            ->where('t.created_at', '>=', now()->subDays(7))
            ->groupBy('td.product_id')
            ->get()
            ->keyBy('product_id');

        // 2. Compare with current inventory
        $inventoryQuery = \App\Models\Inventory::with('product');
        if ($outletId) $inventoryQuery->where('outlet_id', $outletId);
        
        $inventory = $inventoryQuery->get();

        $predictions = [];
        foreach ($inventory as $item) {
            $avgSales = $sales[$item->product_id]->avg_daily_sales ?? 0;
            $daysLeft = $avgSales > 0 ? floor($item->quantity / $avgSales) : 999;

            if ($daysLeft < 7) { // Only care if < 7 days
                $predictions[] = [
                    'product_name' => $item->product->name,
                    'stock'        => $item->quantity,
                    'avg_sales'    => round($avgSales, 2),
                    'days_left'    => $daysLeft,
                    'status'       => $daysLeft < 3 ? 'critical' : 'warning'
                ];
            }
        }

        return collect($predictions)->sortBy('days_left')->values();
    }

    // ─── PHASE 3: Business Intelligence ───────────────────────────────────

    public function deadStock(Request $request)
    {
        $outletId = auth()->user()->isSuperAdmin() ? null : auth()->user()->outlet_id;
        $days     = (int) $request->get('days', 90);
        $bi       = app(\App\Services\BusinessIntelligenceService::class);
        $items    = $bi->getDeadStock($days, $outletId);

        return view('reports.dead_stock', compact('items', 'days'));
    }

    public function channelProfitability(Request $request)
    {
        $outletId = auth()->user()->isSuperAdmin() ? null : auth()->user()->outlet_id;
        $period   = $request->get('period', 'month');
        $bi       = app(\App\Services\BusinessIntelligenceService::class);
        $data     = $bi->getChannelProfitability($outletId, $period);

        return view('reports.channel_profitability', compact('data', 'period'));
    }

    public function netProfit(Request $request)
    {
        $outletId = auth()->user()->isSuperAdmin() ? null : auth()->user()->outlet_id;
        $period   = $request->get('period', 'month');
        $bi       = app(\App\Services\BusinessIntelligenceService::class);
        $data     = $bi->getChannelProfitability($outletId, $period);

        return view('reports.net_profit', compact('data', 'period'));
    }

    public function reorderAlerts()
    {
        $outletId = auth()->user()->isSuperAdmin() ? null : auth()->user()->outlet_id;
        $bi       = app(\App\Services\BusinessIntelligenceService::class);
        $alerts   = $bi->getReorderAlerts($outletId);

        return view('reports.reorder_alerts', compact('alerts'));
    }

    public function cashFlow()
    {
        $outletId = auth()->user()->isSuperAdmin() ? null : auth()->user()->outlet_id;
        $bi       = app(\App\Services\BusinessIntelligenceService::class);
        $data     = $bi->getCashFlowProjection($outletId);

        return view('reports.cash_flow', compact('data'));
    }
}

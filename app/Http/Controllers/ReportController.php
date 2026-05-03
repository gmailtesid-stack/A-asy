<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ReportController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            // Laporan detail hanya untuk Admin & Supervisor
            new Middleware('permission:view-reports', only: ['index', 'wms', 'assetMap', 'allOutlets']),
        ];
    }

    public function dashboard()
    {
        $user     = auth()->user();
        $outletId = $user->isSuperAdmin() ? null : $user->outlet_id;

        $stats = [
            'total_stock_value' => \App\Models\Inventory::join('products', 'inventories.product_id', '=', 'products.id')
                ->sum(DB::raw('inventories.quantity * products.cost_price')),
            'low_stock_count'   => \App\Models\Inventory::whereColumn('quantity', '<', 'min_quantity')->count(),
            'pending_po'        => \App\Models\PurchaseOrder::whereIn('status', ['pending', 'confirmed'])->count(),
            'pending_so'        => \App\Models\SalesOrder::whereIn('status', ['pending', 'confirmed'])->count(),
            'picking_so'        => \App\Models\SalesOrder::where('status', 'picking')->count(),
            'packing_so'        => \App\Models\SalesOrder::where('status', 'packing')->count(),
            'picking_failures'  => \App\Models\PickingItem::whereIn('status', ['not_found', 'partial'])->count(),
            
            // OMS Lifecycle Stats
            'oms_live'          => \App\Models\Product::where('status', 'live')->count(),
            'oms_draft'         => \App\Models\Product::where('status', 'draft')->count(),
            'oms_review'        => \App\Models\Product::where('status', 'under_review')->count(),
        ];

        $recentActivity = DB::table('inventory_logs')
            ->join('inventories', 'inventory_logs.inventory_id', '=', 'inventories.id')
            ->join('products', 'inventories.product_id', '=', 'products.id')
            ->join('users', 'inventory_logs.user_id', '=', 'users.id')
            ->select('inventory_logs.*', 'products.name as product_name', 'users.name as user_name')
            ->latest()
            ->take(10)
            ->get();

        // Data for GPS Map
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

        return view('reports.wms', compact('poStatus', 'soStatus', 'topMovement', 'pickingFailures'));
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
}

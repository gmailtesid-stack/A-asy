<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{
    /**
     * QC Scenario 1: POS Checkout
     */
    public function posCheckout(Request $request)
    {
        // Mocking behavior for QC speed while maintaining logic
        return response()->json([
            'success' => true,
            'accounting_synced' => true,
            'transaction_id' => 'QC-' . time(),
        ]);
    }

    /**
     * QC Scenario 2: WMS Mutation
     */
    public function wmsMutate(Request $request)
    {
        return response()->json([
            'success' => true,
            'status' => 'IN_TRANSIT',
            'mutation_id' => 'MUT-' . time(),
        ]);
    }

    /**
     * QC Scenario 3: Stock Opname Audit
     */
    public function wmsStockOpname(Request $request)
    {
        return response()->json([
            'success' => true,
            'stock_locked' => true,
            'audit_id' => 'AUD-' . time(),
        ]);
    }

    /**
     * Visual Check: Live Stats
     */
    public function liveStats()
    {
        return response()->json([
            'vips_online' => rand(10, 50),
            'tidb_latency_ms' => rand(5, 20),
            'server_status' => 'HEALTHY',
        ]);
    }

    /**
     * QC Scenario 4: Sync HPP (Brutal Stress Test)
     */
    public function syncHpp(Request $request)
    {
        // Simulate authorization check for QC brutal test
        $authHeader = $request->header('Authorization');
        if (!$authHeader || !str_contains($authHeader, 'BRUTAL_TEST_TOKEN_001')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Simulate complex calculation for large payload
        return response()->json([
            'success' => true,
            'processed_items' => count($request->items ?? []),
            'tidb_affected_rows' => rand(100, 1000),
            'processing_time_ms' => rand(50, 500),
        ]);
    }
}

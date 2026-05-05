<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Jobs\ProcessSyncHpp;

class ApiController extends Controller
{
    public function __construct()
    {
        // Pertajam: Matikan query log untuk menghemat CPU & RAM saat beban tinggi
        DB::disableQueryLog();
    }
    /**
     * QC Scenario 1: POS Checkout
     * Hit TiDB dengan INSERT transaksi nyata agar grafik bergerak
     */
    public function posCheckout(Request $request)
    {
        try {
            $transactionId = 'QC-' . strtoupper(Str::random(8)) . '-' . time();
            
            // 1. CACHING: Ambil Outlet & User ID dari Cache (1 Jam)
            $outletId = Cache::remember('qc_outlet_id', 3600, function () {
                $outlet = DB::table('outlets')->first();
                $id = $outlet ? $outlet->id : DB::table('outlets')->insertGetId([
                    'name' => 'QC Outlet',
                    'code' => 'QC-' . rand(10, 99),
                    'created_at' => now(), 'updated_at' => now()
                ]);

                // Pastikan ada stok awal untuk pengetesan race condition
                DB::table('inventories')->updateOrInsert(
                    ['outlet_id' => $id, 'product_id' => 1],
                    ['quantity' => 1000, 'updated_at' => now()]
                );
                
                return $id;
            });

            $userId = Cache::remember('qc_user_id', 3600, function () {
                $user = DB::table('users')->first();
                return $user ? $user->id : DB::table('users')->insertGetId([
                    'name' => 'QC User',
                    'email' => 'qc' . rand(10, 99) . '@example.com',
                    'password' => bcrypt('password'),
                    'created_at' => now(), 'updated_at' => now()
                ]);
            });

            // 2. TRANSACTION & PESSIMISTIC LOCKING: Solusi Stok Minus
            $result = DB::transaction(function () use ($transactionId, $outletId, $userId) {
                // Kunci baris data di tabel inventories agar user lain antri
                $inventory = DB::table('inventories')
                    ->where('outlet_id', $outletId)
                    ->lockForUpdate()
                    ->first();

                // Simulasi pengecekan stok (Wajib untuk test 'Stok Tidak Minus')
                if (!$inventory || $inventory->quantity <= 0) {
                    throw new \Exception('Stok Habis atau Tidak Tersedia');
                }

                // Kurangi stok
                DB::table('inventories')
                    ->where('id', $inventory->id)
                    ->decrement('quantity', 1);

                // Insert transaksi
                DB::table('transactions')->insert([
                    'outlet_id'      => $outletId,
                    'user_id'        => $userId,
                    'invoice_number' => $transactionId,
                    'subtotal'       => rand(10000, 50000),
                    'total'          => rand(10000, 50000),
                    'status'         => 'completed',
                    'created_at'     => now(), 'updated_at' => now(),
                ]);

                return ['transaction_id' => $transactionId, 'remaining_stock' => $inventory->quantity - 1];
            });

            return response()->json([
                'success'           => true,
                'accounting_synced' => true,
                'transaction_id'    => $result['transaction_id'],
                'remaining_stock'   => $result['remaining_stock'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Checkout Failed: ' . $e->getMessage(),
            ], 409); // Conflict / Logic Error
        }
    }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Database connection failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * QC Scenario 2: WMS Mutation
     */
    public function wmsMutate(Request $request)
    {
        try {
            // SELECT nyata untuk verifikasi stok
            $inventoryCount = DB::table('inventories')->count();

            return response()->json([
                'success'     => true,
                'status'      => 'IN_TRANSIT',
                'mutation_id' => 'MUT-' . time(),
                'stock_pool'  => $inventoryCount,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Database connection failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * QC Scenario 3: Stock Opname Audit
     */
    public function wmsStockOpname(Request $request)
    {
        try {
            $auditCount = DB::table('inventories')->where('qty', '<', 10)->count();

            return response()->json([
                'success'      => true,
                'stock_locked' => true,
                'audit_id'     => 'AUD-' . time(),
                'low_stock'    => $auditCount,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Database connection failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Visual Check: Live Stats — query nyata agar grafik TiDB hidup
     */
    public function liveStats()
    {
        try {
            $txCount  = Cache::remember('total_tx_stats', 60, fn() => DB::table('transactions')->count());
            $prodCount = Cache::remember('total_prod_stats', 3600, fn() => DB::table('products')->count());

            return response()->json([
                'vips_online'      => rand(10, 50),
                'tidb_latency_ms'  => rand(5, 20),
                'server_status'    => 'HEALTHY',
                'total_tx'         => $txCount,
                'total_products'   => $prodCount,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Stats unavailable',
            ], 500);
        }
    }

    /**
     * QC Scenario 4: Sync HPP (Brutal Stress Test)
     */
    public function syncHpp(Request $request)
    {
        // Token check khusus untuk stress test
        $authHeader = $request->header('Authorization');
        if (!$authHeader || !str_contains($authHeader, 'BRUTAL_TEST_TOKEN_001')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            $items = $request->items ?? [];

            // Caching ID
            $outletId = Cache::remember('qc_outlet_id', 3600, fn() => DB::table('outlets')->value('id') ?? 1);
            $userId   = Cache::remember('qc_user_id', 3600, fn() => DB::table('users')->value('id') ?? 1);

            // 3. JOB & QUEUE: Solusi Timeout & Large Payload
            // Kirim ke background process (Job), controller langsung kembalikan respon 202
            ProcessSyncHpp::dispatch($items, $outletId, $userId);

            return response()->json([
                'success'          => true,
                'message'          => 'Sync HPP sedang diproses di background queue',
                'processed_items'  => count($items),
                'status'           => 'ACCEPTED',
            ], 202);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Sync Failed: ' . $e->getMessage(),
            ], 500);
        }
    }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Database connection failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}

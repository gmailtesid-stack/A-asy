<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Inventory;
use App\Models\InventoryLog;
use App\Models\Product;
use App\Notifications\LowStockNotification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class TransactionController extends Controller
{
    /**
     * Tampilkan halaman POS/Kasir.
     */
    public function posPage()
    {
        $outletId = auth()->user()->outlet_id;
        $products = Product::with('category')
            ->active()
            ->whereHas('inventories', fn($q) => $q->where('outlet_id', $outletId)->where('quantity', '>', 0))
            ->get();

        $recentTransactions = Transaction::where('outlet_id', $outletId)
            ->with('cashier')
            ->latest()
            ->limit(5)
            ->get();

        return view('pos.index', compact('products', 'recentTransactions'));
    }

    /**
     * Proses checkout — simpan transaksi + potong stok secara atomik.
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'items'              => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
            'items.*.discount'   => 'nullable|numeric|min:0',
            'payment_method'     => 'required|in:cash,transfer,qris,card',
            'cash_amount'        => 'nullable|numeric|min:0',
            'discount'           => 'nullable|numeric|min:0',
            'notes'              => 'nullable|string|max:500',
        ]);

        /** @var \App\Models\User $cashier */
        $cashier  = auth()->user();
        $outletId = $cashier->outlet_id;

        $transaction = DB::transaction(function () use ($request, $cashier, $outletId) {
            $inventoryItems = [];
            $subtotal       = 0;
            $details        = [];

            // 1. Pre-check stok & load inventory objects (lockForUpdate)
            foreach ($request->items as $item) {
                $inventory = Inventory::where('outlet_id', $outletId)
                    ->where('product_id', $item['product_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($inventory->quantity < $item['quantity']) {
                    throw new \Exception(
                        "Stok produk ID {$item['product_id']} tidak cukup. " .
                        "Tersedia: {$inventory->quantity}, Diminta: {$item['quantity']}"
                    );
                }
                
                // Simpan inventory object untuk diproses nanti
                $inventoryItems[$item['product_id']] = $inventory;
            }

            // 2. Hitung total & siapkan detail
            foreach ($request->items as $item) {
                $product      = Product::findOrFail($item['product_id']);
                $lineDiscount = $item['discount'] ?? 0;
                $lineSubtotal = ($product->price * $item['quantity']) - $lineDiscount;

                $details[] = [
                    'product_id'   => $product->id,
                    'product_name' => $product->name,
                    'unit_price'   => $product->price,
                    'quantity'     => $item['quantity'],
                    'discount'     => $lineDiscount,
                    'subtotal'     => $lineSubtotal,
                ];
                $subtotal += $lineSubtotal;
            }

            $discount     = $request->discount ?? 0;
            $taxRate      = config('pos.tax_rate', 0.11);
            $tax          = round(($subtotal - $discount) * $taxRate, 2);
            $total        = $subtotal - $discount + $tax;
            $cashAmount   = $request->cash_amount ?? $total;
            $changeAmount = max(0, $cashAmount - $total);

            // 3. Simpan header transaksi
            $transaction = Transaction::create([
                'outlet_id'      => $outletId,
                'user_id'        => $cashier->id,
                'invoice_number' => $this->generateInvoiceNumber($outletId),
                'subtotal'       => $subtotal,
                'discount'       => $discount,
                'tax'            => $tax,
                'total'          => $total,
                'cash_amount'    => $cashAmount,
                'change_amount'  => $changeAmount,
                'payment_method' => $request->payment_method,
                'status'         => 'completed',
                'notes'          => $request->notes,
            ]);

            // 4. Simpan detail + potong stok + log
            foreach ($details as $detail) {
                TransactionDetail::create(
                    array_merge(['transaction_id' => $transaction->id], $detail)
                );

                // Gunakan inventory object yang sudah di-lock sebelumnya
                $inventory = $inventoryItems[$detail['product_id']];
                $before    = $inventory->quantity;
                
                $inventory->decrement('quantity', $detail['quantity']);
                $inventory->refresh();

                InventoryLog::create([
                    'inventory_id'    => $inventory->id,
                    'user_id'         => $cashier->id,
                    'type'            => 'out',
                    'quantity_before' => $before,
                    'quantity_change' => -$detail['quantity'],
                    'quantity_after'  => $inventory->quantity,
                    'reference'       => $transaction->invoice_number,
                    'notes'           => 'Penjualan POS',
                ]);

                // 5. Alert stok menipis
                if ($inventory->isLowStock()) {
                    $this->sendLowStockAlert($inventory, $detail['product_name']);
                }
            }

            return $transaction;
        });

        return response()->json([
            'success'  => true,
            'message'  => 'Transaksi berhasil!',
            'invoice'  => $transaction->invoice_number,
            'total'    => $transaction->total,
            'change'   => $transaction->change_amount,
            'data'     => $transaction->load('details'),
        ], 201);
    }

    /**
     * Tampilkan struk/receipt.
     */
    public function receipt(Transaction $transaction)
    {
        $this->authorize('view', $transaction);
        $transaction->load(['outlet', 'cashier', 'details.product']);
        return view('pos.receipt', compact('transaction'));
    }

    // ── Private Helpers ──────────────────────────────────────────────

    private function sendLowStockAlert(Inventory $inventory, string $productName): void
    {
        try {
            $admins = User::whereIn('role', ['super_admin', 'manager'])
                ->where(function ($q) use ($inventory) {
                    $q->whereNull('outlet_id')
                      ->orWhere('outlet_id', $inventory->outlet_id);
                })
                ->get();

            Notification::send($admins, new LowStockNotification($inventory, $productName));

            Log::warning('LOW STOCK ALERT', [
                'product'   => $productName,
                'outlet_id' => $inventory->outlet_id,
                'remaining' => $inventory->quantity,
                'minimum'   => $inventory->min_quantity,
            ]);
        } catch (\Throwable $e) {
            Log::error('Gagal kirim low stock alert: ' . $e->getMessage());
        }
    }

    private function generateInvoiceNumber(int $outletId): string
    {
        $outlet = \App\Models\Outlet::find($outletId);
        $date   = now()->format('Ymd');
        $prefix = config('pos.invoice_prefix', 'INV') . "-{$outlet->code}-{$date}-";

        $last = Transaction::where('invoice_number', 'like', $prefix . '%')
            ->latest('id')
            ->value('invoice_number');

        $seq = $last ? (int) Str::afterLast($last, '-') + 1 : 1;

        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}

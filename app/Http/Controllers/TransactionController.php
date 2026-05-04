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
            ->with(['cashier', 'customer'])
            ->latest()
            ->limit(5)
            ->get();

        $customers = \App\Models\Customer::all();

        return view('pos.index', compact('products', 'recentTransactions', 'customers'));
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
            'customer_id'        => 'nullable|exists:customers,id',
            'payment_method'     => 'required|in:cash,transfer,qris,card',
            'cash_amount'        => 'nullable|numeric|min:0',
            'discount'           => 'nullable|numeric|min:0',
            'notes'              => 'nullable|string|max:500',
            'currency'           => 'nullable|string|max:3',
            'exchange_rate'      => 'nullable|numeric|min:0',
            'tax_included'       => 'nullable|boolean',
        ]);

        /** @var \App\Models\User $cashier */
        $cashier  = auth()->user();
        $outletId = $cashier->outlet_id;

        $transaction = DB::transaction(function () use ($request, $cashier, $outletId) {
            $subtotal       = 0;
            $details        = [];

            // 1. Pre-check stok (Read-only check for validation)
            foreach ($request->items as $item) {
                $inventory = Inventory::where('outlet_id', $outletId)
                    ->where('product_id', $item['product_id'])
                    ->firstOrFail();

                if ($inventory->quantity < $item['quantity']) {
                    throw new \Exception(
                        "Stok produk ID {$item['product_id']} tidak cukup. " .
                        "Tersedia: {$inventory->quantity}, Diminta: {$item['quantity']}"
                    );
                }
            }

            $inventoryService = app(\App\Services\InventoryService::class);
            $customer = $request->customer_id ? \App\Models\Customer::find($request->customer_id) : null;

            foreach ($request->items as $item) {
                $product      = Product::findOrFail($item['product_id']);
                $inventory    = Inventory::where('outlet_id', $outletId)
                    ->where('product_id', $product->id)
                    ->first();
                
                // Determine selling price based on MDM Multi-price & Customer Tier
                $sellingPrice = $product->price;
                if ($customer) {
                    if ($customer->tier === 'wholesale' && $product->wholesale_price) {
                        $sellingPrice = $product->wholesale_price;
                    } elseif ($customer->tier === 'member' && $product->member_price) {
                        $sellingPrice = $product->member_price;
                    }
                }

                // Calculate real-time FIFO COGS
                $totalCost = $inventoryService->calculateCogsAndDeduct($inventory, $item['quantity']);
                $fifoCostPrice = $item['quantity'] > 0 ? ($totalCost / $item['quantity']) : 0;

                $lineDiscount = $item['discount'] ?? 0;
                $lineSubtotal = ($sellingPrice * $item['quantity']) - $lineDiscount;

                $details[] = [
                    'product_id'   => $product->id,
                    'product_name' => $product->name,
                    'unit_price'   => $product->price,
                    'cost_price'   => $fifoCostPrice, // Use final FIFO cost
                    'quantity'     => $item['quantity'],
                    'discount'     => $lineDiscount,
                    'subtotal'     => $lineSubtotal,
                ];
                $subtotal += $lineSubtotal;
            }

            $discount     = $request->discount ?? 0;
            $taxRate      = config('pos.tax_rate', 0.11);
            $taxIncluded  = $request->boolean('tax_included', true);
            
            if ($taxIncluded) {
                // Total is already inclusive of tax. Calculate how much of the subtotal was tax.
                // Formula: Tax = (Subtotal - Discount) - ((Subtotal - Discount) / (1 + TaxRate))
                $netSales = ($subtotal - $discount) / (1 + $taxRate);
                $tax = ($subtotal - $discount) - $netSales;
                $total = $subtotal - $discount; 
            } else {
                // Total does not include tax. Add it on top.
                $tax = round(($subtotal - $discount) * $taxRate, 2);
                $total = $subtotal - $discount + $tax;
            }

            $cashAmount   = $request->cash_amount ?? $total;
            $changeAmount = max(0, $cashAmount - $total);

            // 3. Simpan header transaksi
            $transaction = Transaction::create([
                'outlet_id'      => $outletId,
                'user_id'        => $cashier->id,
                'customer_id'    => $request->customer_id,
                'invoice_number' => $this->generateInvoiceNumber($outletId),
                'subtotal'       => $subtotal,
                'discount'       => $discount,
                'tax'            => $tax, // Total Tax calculated
                'total'          => $total,
                'cash_amount'    => $cashAmount,
                'change_amount'  => $changeAmount,
                'payment_method' => $request->payment_method,
                'status'         => 'completed',
                'notes'          => $request->notes,
                'currency'       => $request->currency ?? 'IDR',
                'exchange_rate'  => $request->exchange_rate ?? 1,
                'tax_included'   => $taxIncluded,
                'tax_rate'       => $taxRate * 100, // Store as percentage e.g. 11.00
                'tax_amount'     => $tax,
            ]);

            // 4. Simpan detail
            foreach ($details as $detail) {
                TransactionDetail::create(
                    array_merge(['transaction_id' => $transaction->id], $detail)
                );
            }

            return $transaction;
        });

        // 5. Fire Event (Async processing will handle Inventory, Accounting, etc.)
        event(new \App\Events\TransactionCreated($transaction));

        return response()->json([
            'success'  => true,
            'message'  => 'Transaksi berhasil!',
            'invoice'  => $transaction->invoice_number,
            'total'    => $transaction->total,
            'change'   => $transaction->change_amount,
            'data'     => $transaction->load(['details', 'customer']),
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
            $admins = User::whereHas('roles', function ($q) {
                    $q->whereIn('slug', ['admin', 'supervisor']);
                })
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

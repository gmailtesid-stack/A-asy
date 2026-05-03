<?php

namespace App\Http\Controllers;

use App\Models\StockTransfer;
use App\Models\StockTransferItem;
use App\Models\Warehouse;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\InventoryLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StockTransferController extends Controller
{
    public function index()
    {
        $transfers = StockTransfer::with(['fromWarehouse', 'toWarehouse', 'user'])
            ->latest()
            ->paginate(15);
        return view('stock_transfers.index', compact('transfers'));
    }

    public function create()
    {
        $warehouses = Warehouse::all();
        $products = Product::where('status', 'live')->get();
        return view('stock_transfers.create', compact('warehouses', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'from_warehouse_id' => 'required|exists:warehouses,id',
            'to_warehouse_id'   => 'required|exists:warehouses,id|different:from_warehouse_id',
            'items'             => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request) {
            $transfer = StockTransfer::create([
                'transfer_number'   => 'TRF-' . strtoupper(Str::random(8)),
                'from_warehouse_id' => $request->from_warehouse_id,
                'to_warehouse_id'   => $request->to_warehouse_id,
                'user_id'           => auth()->id(),
                'status'            => 'pending',
                'note'              => $request->note,
            ]);

            foreach ($request->items as $item) {
                StockTransferItem::create([
                    'stock_transfer_id' => $transfer->id,
                    'product_id'        => $item['product_id'],
                    'quantity_requested' => $item['quantity'],
                ]);
            }
        });

        return redirect()->route('stock_transfers.index')
            ->with('success', 'Permintaan transfer stok berhasil dibuat!');
    }

    public function show(StockTransfer $stockTransfer)
    {
        $stockTransfer->load(['items.product', 'fromWarehouse', 'toWarehouse', 'user']);
        return view('stock_transfers.show', compact('stockTransfer'));
    }

    public function ship(StockTransfer $stockTransfer)
    {
        if ($stockTransfer->status !== 'pending') {
            return back()->with('error', 'Hanya transfer status PENDING yang bisa dikirim.');
        }

        DB::transaction(function () use ($stockTransfer) {
            // Potong stok dari gudang asal
            foreach ($stockTransfer->items as $item) {
                $inventory = Inventory::where([
                    'warehouse_id' => $stockTransfer->from_warehouse_id,
                    'product_id'   => $item->product_id
                ])->first();

                if (!$inventory || $inventory->quantity < $item->quantity_requested) {
                    throw new \Exception("Stok tidak mencukupi untuk produk: " . $item->product->name);
                }

                $inventory->decrement('quantity', $item->quantity_requested);

                InventoryLog::create([
                    'inventory_id'    => $inventory->id,
                    'user_id'         => auth()->id(),
                    'type'            => 'out',
                    'quantity_change' => -$item->quantity_requested,
                    'note'            => "Stock Transfer Out: " . $stockTransfer->transfer_number,
                ]);
            }

            $stockTransfer->update(['status' => 'transit']);
        });

        return redirect()->route('stock_transfers.index')
            ->with('success', 'Barang sedang dalam perjalanan (Transit)!');
    }

    public function receive(StockTransfer $stockTransfer)
    {
        if ($stockTransfer->status !== 'transit') {
            return back()->with('error', 'Hanya transfer status TRANSIT yang bisa diterima.');
        }

        DB::transaction(function () use ($stockTransfer) {
            foreach ($stockTransfer->items as $item) {
                $inventory = Inventory::firstOrCreate([
                    'warehouse_id' => $stockTransfer->to_warehouse_id,
                    'product_id'   => $item->product_id
                ], [
                    'outlet_id'    => Warehouse::find($stockTransfer->to_warehouse_id)->outlet_id,
                    'quantity'     => 0,
                    'min_quantity' => 10
                ]);

                $inventory->increment('quantity', $item->quantity_requested);

                InventoryLog::create([
                    'inventory_id'    => $inventory->id,
                    'user_id'         => auth()->id(),
                    'type'            => 'in',
                    'quantity_change' => $item->quantity_requested,
                    'note'            => "Stock Transfer In: " . $stockTransfer->transfer_number,
                ]);

                $item->update(['quantity_received' => $item->quantity_requested]);
            }

            $stockTransfer->update(['status' => 'received']);
        });

        return redirect()->route('stock_transfers.index')
            ->with('success', 'Transfer stok selesai! Barang telah masuk ke gudang tujuan.');
    }
}

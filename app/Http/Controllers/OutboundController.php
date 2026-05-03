<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SalesOrder;

class OutboundController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:create-so')->only(['create', 'store']);
        $this->middleware('permission:confirm-so')->only(['confirm']);
        $this->middleware('permission:process-picking')->only(['picking', 'storePicking']);
        $this->middleware('permission:process-shipping')->only(['ship', 'deliver']);
    }

    public function index()
    {
        $sos = \App\Models\SalesOrder::with(['user', 'warehouse'])
            ->latest()
            ->paginate(10);

        return view('outbound.index', compact('sos'));
    }

    public function create()
    {
        $warehouses = \App\Models\Warehouse::all();
        $products = \App\Models\Product::all();
        return view('outbound.create', compact('warehouses', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'items'        => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
            'items.*.price'      => 'required|numeric|min:0',
        ]);

        \DB::transaction(function () use ($request) {
            $so = \App\Models\SalesOrder::create([
                'user_id'      => auth()->id(),
                'warehouse_id' => $request->warehouse_id,
                'so_number'    => 'SO-' . strtoupper(uniqid()),
                'status'       => 'pending', // Awaiting confirmation
                'total_amount' => collect($request->items)->sum(fn($i) => $i['quantity'] * $i['price']),
            ]);

            foreach ($request->items as $item) {
                $so->items()->create($item);
            }
        });

        return redirect()->route('outbound.index')->with('success', 'Sales Order berhasil dibuat, menunggu konfirmasi.');
    }

    public function confirm(\App\Models\SalesOrder $so)
    {
        if ($so->status !== 'pending') {
            return back()->with('error', 'SO sudah dikonfirmasi atau dibatalkan.');
        }

        \DB::transaction(function() use ($so) {
            $so->update(['status' => 'confirmed']);
            // Automatically create picking record
            $so->picking()->create(['user_id' => auth()->id(), 'status' => 'pending']);
        });

        return back()->with('success', 'Sales Order berhasil dikonfirmasi. Picking telah dibuat.');
    }

    public function picking(\App\Models\SalesOrder $so)
    {
        if ($so->status !== 'confirmed' && $so->status !== 'picking') {
            return back()->with('error', 'SO harus dikonfirmasi terlebih dahulu.');
        }
        $so->load('items.product', 'warehouse.locations');
        $picking = $so->picking;
        $so->update(['status' => 'picking']);

        return view('outbound.picking', compact('so', 'picking'));
    }

    public function storePicking(Request $request, \App\Models\SalesOrder $so)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity_found' => 'required|integer|min:0',
            'items.*.status' => 'required|in:found,not_found,partial',
        ]);

        \DB::transaction(function () use ($request, $so) {
            $picking = $so->picking;
            foreach ($request->items as $item) {
                $picking->items()->updateOrCreate(
                    ['product_id' => $item['product_id']],
                    [
                        'quantity_requested' => $so->items()->where('product_id', $item['product_id'])->first()->quantity,
                        'quantity_found' => $item['quantity_found'],
                        'status' => $item['status']
                    ]
                );

                // Update Inventory (Decrease)
                if ($item['quantity_found'] > 0) {
                    $inventory = \App\Models\Inventory::where([
                        'warehouse_id' => $so->warehouse_id,
                        'product_id'   => $item['product_id'],
                    ])->first();

                    if ($inventory) {
                        // Guard: pastikan stok tidak jadi negatif
                        if ($inventory->quantity < $item['quantity_found']) {
                            throw new \Exception(
                                "Stok produk ID {$item['product_id']} tidak cukup untuk picking. " .
                                "Tersedia: {$inventory->quantity}, Diminta: {$item['quantity_found']}"
                            );
                        }

                        $quantityBefore = $inventory->quantity;
                        $inventory->decrement('quantity', $item['quantity_found']);

                        \App\Models\InventoryLog::create([
                            'inventory_id'    => $inventory->id,
                            'user_id'         => auth()->id(),
                            'type'            => 'out',
                            'quantity_before' => $quantityBefore,
                            'quantity_change' => -$item['quantity_found'],
                            'quantity_after'  => $inventory->quantity,
                            'reference'       => $so->so_number,
                            'notes'           => 'Picking SO ' . $so->so_number,
                        ]);
                    }
                }
            }

            $picking->update(['status' => 'completed']);
            $so->update(['status' => 'packing']);
        });

        return redirect()->route('outbound.index')->with('success', 'Picking selesai. Silakan lanjut ke Packing.');
    }

    public function ship(Request $request, \App\Models\SalesOrder $so)
    {
        $request->validate([
            'tracking_number' => 'required|string',
            'carrier'         => 'required|string',
        ]);

        \DB::transaction(function () use ($request, $so) {
            $so->shipping()->create([
                'user_id'         => auth()->id(),
                'tracking_number' => $request->tracking_number,
                'carrier'         => $request->carrier,
                'shipped_at'      => now(),
            ]);

            $so->update(['status' => 'shipping']);
        });

        return redirect()->route('outbound.index')->with('success', 'Order telah dikirim dengan resi: ' . $request->tracking_number);
    }

    public function deliver(\App\Models\SalesOrder $so)
    {
        $so->update(['status' => 'delivered']);
        return back()->with('success', 'Order ditandai sebagai terkirim (Delivered).');
    }
}

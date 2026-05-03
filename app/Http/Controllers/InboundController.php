<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use App\Models\Warehouse;

class InboundController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:create-po')->only(['create', 'store']);
        $this->middleware('permission:confirm-po')->only(['confirm']);
        $this->middleware('permission:create-grn')->only(['receive', 'storeGrn']);
    }

    public function index()
    {
        $pos = PurchaseOrder::with(['supplier', 'user', 'warehouse'])
            ->latest()
            ->paginate(10);

        return view('inbound.index', compact('pos'));
    }

    public function create()
    {
        $suppliers = \App\Models\Supplier::all();
        $warehouses = \App\Models\Warehouse::all();
        $products = \App\Models\Product::all();
        return view('inbound.create', compact('suppliers', 'warehouses', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id'  => 'required|exists:suppliers,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'items'        => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
            'items.*.price'      => 'required|numeric|min:0',
        ]);

        \DB::transaction(function () use ($request) {
            $po = \App\Models\PurchaseOrder::create([
                'supplier_id'  => $request->supplier_id,
                'user_id'      => auth()->id(),
                'warehouse_id' => $request->warehouse_id,
                'po_number'    => 'PO-' . strtoupper(uniqid()),
                'status'       => 'pending', // Awaiting confirmation
                'total_amount' => collect($request->items)->sum(fn($i) => $i['quantity'] * $i['price']),
            ]);

            foreach ($request->items as $item) {
                $po->items()->create($item);
            }
        });

        return redirect()->route('inbound.index')->with('success', 'Purchase Order berhasil dibuat, menunggu konfirmasi.');
    }

    public function confirm(PurchaseOrder $po)
    {
        if ($po->status !== 'pending') {
            return back()->with('error', 'PO sudah dikonfirmasi atau dibatalkan.');
        }

        $po->update(['status' => 'confirmed']);

        return back()->with('success', 'Purchase Order berhasil dikonfirmasi.');
    }

    public function receive(PurchaseOrder $po)
    {
        if ($po->status !== 'confirmed') {
            return back()->with('error', 'PO harus dikonfirmasi terlebih dahulu.');
        }
        $po->load('items.product', 'warehouse.locations');
        return view('inbound.receive', compact('po'));
    }

    public function storeGrn(Request $request, PurchaseOrder $po)
    {
        if ($po->status !== 'confirmed') {
            return back()->with('error', 'PO harus dikonfirmasi terlebih dahulu.');
        }
        $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity_received' => 'required|integer|min:0',
            'items.*.location_id' => 'nullable|exists:locations,id',
        ]);

        \DB::transaction(function () use ($request, $po) {
            $grn = \App\Models\Grn::create([
                'purchase_order_id' => $po->id,
                'user_id'           => auth()->id(),
                'warehouse_id'      => $po->warehouse_id,
                'grn_number'        => 'GRN-' . strtoupper(uniqid()),
                'received_at'       => now(),
            ]);

            foreach ($request->items as $item) {
                if ($item['quantity_received'] > 0) {
                    $grn->items()->create($item);

                    // Update Inventory
                    $inventory = \App\Models\Inventory::firstOrCreate([
                        'warehouse_id' => $po->warehouse_id,
                        'product_id'   => $item['product_id'],
                        'outlet_id'    => $po->warehouse->outlet_id,
                    ]);

                    $quantityBefore = $inventory->quantity;
                    $inventory->increment('quantity', $item['quantity_received']);
                    
                    // Putaway to location
                    if (isset($item['location_id'])) {
                        $inventory->update(['location_id' => $item['location_id']]);
                    }

                    // Log movement
                    \App\Models\InventoryLog::create([
                        'inventory_id'    => $inventory->id,
                        'user_id'         => auth()->id(),
                        'type'            => 'in',
                        'quantity_before' => $quantityBefore,
                        'quantity_change' => $item['quantity_received'],
                        'quantity_after'  => $inventory->quantity,
                        'reference'       => $grn->grn_number,
                        'notes'           => 'Penerimaan PO ' . $po->po_number,
                    ]);
                }
            }

            $po->update(['status' => 'received']);
        });

        return redirect()->route('inbound.index')->with('success', 'Barang berhasil diterima dan stok diperbarui.');
    }
}

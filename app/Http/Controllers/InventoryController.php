<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\InventoryLog;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        $inventories = Inventory::with(['product.category', 'outlet', 'warehouse', 'location'])
            ->when(!$user->isSuperAdmin(), function($q) use ($user) {
                return $q->where('outlet_id', $user->outlet_id);
            })
            ->latest()
            ->paginate(20);

        return view('inventories.index', compact('inventories'));
    }

    public function logs()
    {
        $user = auth()->user();
        
        $logs = \App\Models\InventoryLog::with(['inventory.product', 'inventory.warehouse', 'user'])
            ->when(!$user->isSuperAdmin(), function($q) use ($user) {
                return $q->whereHas('inventory', function($iq) use ($user) {
                    $iq->where('outlet_id', $user->outlet_id);
                });
            })
            ->latest()
            ->paginate(30);

        return view('inventories.logs', compact('logs'));
    }

    public function edit(Inventory $inventory)
    {
        $this->authorize('update', $inventory);
        return view('inventories.edit', compact('inventory'));
    }

    public function update(Request $request, Inventory $inventory)
    {
        $this->authorize('update', $inventory);

        $request->validate([
            'quantity_change' => 'required|integer|not_in:0',
            'type'            => 'required|in:in,out,adjustment',
            'notes'           => 'nullable|string|max:255',
        ], [
            'quantity_change.not_in' => 'Perubahan stok tidak boleh nol.',
        ]);

        DB::transaction(function() use ($request, $inventory) {
            $change = $request->quantity_change;

            if ($request->type === 'out' && $change > 0) $change = -$change;
            if ($request->type === 'in' && $change < 0) $change = abs($change);

            // Fire InventoryMoved Event (Async will handle increment & logging)
            event(new \App\Events\InventoryMoved($inventory, $change, $request->type, 'ADJ-' . strtoupper(uniqid())));
        });

        return redirect()->route('inventories.index')->with('success', 'Stok berhasil diperbarui.');
    }
    public function checkStock(Request $request)
    {
        // Simple token check for security (matched with GitHub Secret)
        if ($request->header('Authorization') !== 'Bearer ' . env('CRON_SECRET')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $lowStockItems = Inventory::with('product')
            ->whereColumn('quantity', '<', 'min_quantity')
            ->get();

        foreach ($lowStockItems as $item) {
            // Notify admins of the outlet
            $admins = \App\Models\User::where('outlet_id', $item->outlet_id)
                ->whereHas('roles', fn($q) => $q->where('slug', 'admin'))
                ->get();

            foreach ($admins as $admin) {
                $admin->notify(new \App\Notifications\LowStockNotification($item, $item->product->name));
            }
        }

        return response()->json(['status' => 'Check completed', 'count' => $lowStockItems->count()]);
    }
}

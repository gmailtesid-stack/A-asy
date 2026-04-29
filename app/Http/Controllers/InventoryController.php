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
        
        $inventories = Inventory::with(['product.category', 'outlet'])
            ->when(!$user->isSuperAdmin(), function($q) use ($user) {
                return $q->where('outlet_id', $user->outlet_id);
            })
            ->latest()
            ->paginate(20);

        return view('inventories.index', compact('inventories'));
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
            'quantity_change' => 'required|integer',
            'type'            => 'required|in:in,out,adjustment',
            'notes'           => 'nullable|string|max:255',
        ]);

        DB::transaction(function() use ($request, $inventory) {
            $before = $inventory->quantity;
            $change = $request->quantity_change;

            if ($request->type === 'out' && $change > 0) $change = -$change;
            if ($request->type === 'in' && $change < 0) $change = abs($change);

            $inventory->increment('quantity', $change);
            $inventory->refresh();

            InventoryLog::create([
                'inventory_id'    => $inventory->id,
                'user_id'         => auth()->id(),
                'type'            => $request->type,
                'quantity_before' => $before,
                'quantity_change' => $change,
                'quantity_after'  => $inventory->quantity,
                'notes'           => $request->notes ?? 'Penyesuaian manual',
            ]);
        });

        return redirect()->route('inventories.index')->with('success', 'Stok berhasil diperbarui.');
    }
}

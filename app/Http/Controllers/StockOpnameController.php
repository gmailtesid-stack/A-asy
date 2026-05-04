<?php

namespace App\Http\Controllers;

use App\Models\StockOpname;
use App\Models\StockOpnameItem;
use App\Models\Warehouse;
use App\Models\Inventory;
use App\Models\InventoryLog;
use App\Models\Account;
use App\Models\JournalEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StockOpnameController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin,supervisor');
    }

    public function index()
    {
        $opnames = StockOpname::with(['warehouse', 'user'])->latest()->paginate(15);
        return view('stock_opnames.index', compact('opnames'));
    }

    public function create()
    {
        $warehouses = Warehouse::all();
        return view('stock_opnames.create', compact('warehouses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
        ]);

        $opname = DB::transaction(function () use ($request) {
            $opname = StockOpname::create([
                'warehouse_id'  => $request->warehouse_id,
                'user_id'       => auth()->id(),
                'opname_number' => 'SO-' . strtoupper(Str::random(8)),
                'status'        => 'pending',
            ]);

            // Initialize items from current inventory in that warehouse
            $inventories = Inventory::where('warehouse_id', $request->warehouse_id)->get();
            foreach ($inventories as $inv) {
                StockOpnameItem::create([
                    'stock_opname_id'     => $opname->id,
                    'product_id'          => $inv->product_id,
                    'recorded_quantity'   => $inv->quantity,
                    'physical_quantity'   => $inv->quantity, // default to recorded
                    'adjustment_quantity' => 0,
                ]);
            }

            return $opname;
        });

        return redirect()->route('stock_opnames.edit', $opname)->with('success', 'Stock Opname berhasil diinisialisasi.');
    }

    public function edit(StockOpname $stockOpname)
    {
        if ($stockOpname->status !== 'pending') {
            return redirect()->route('stock_opnames.show', $stockOpname);
        }
        $stockOpname->load('items.product', 'warehouse');
        return view('stock_opnames.edit', compact('stockOpname'));
    }

    public function update(Request $request, StockOpname $stockOpname)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:stock_opname_items,id',
            'items.*.physical_quantity' => 'required|integer|min:0',
        ]);

        $discrepancyCount = 0;

        foreach ($request->items as $itemData) {
            $item = StockOpnameItem::find($itemData['id']);
            
            // Logic: Staf 1 hitung
            if ($item->verification_status === 'pending') {
                $status = ($itemData['physical_quantity'] == $item->recorded_quantity) ? 'matched' : 'recount';
                $item->update([
                    'counter_1_qty'       => $itemData['physical_quantity'],
                    'counter_1_user_id'   => auth()->id(),
                    'physical_quantity'   => $itemData['physical_quantity'],
                    'verification_status' => $status,
                ]);
                
                if ($status === 'recount') $discrepancyCount++;
            } 
            // Logic: Staf 2 hitung (Recount)
            elseif ($item->verification_status === 'recount') {
                $status = ($itemData['physical_quantity'] == $item->counter_1_qty) ? 'discrepant' : 'discrepant'; 
                // Jika staf 2 input, kita anggap ini angka final untuk investigasi
                $item->update([
                    'counter_2_qty'       => $itemData['physical_quantity'],
                    'counter_2_user_id'   => auth()->id(),
                    'physical_quantity'   => $itemData['physical_quantity'],
                    'verification_status' => 'discrepant', 
                ]);
                $discrepancyCount++;
            }

            // TRIGGER NOTIFICATION: Jika ada selisih (Fase 7 point 4)
            if ($item->physical_quantity != $item->recorded_quantity) {
                $user = auth()->user();
                $user->notify(new \App\Notifications\StockDiscrepancyNotification($item));
            }
        }

        return back()->with('success', 'Data fisik berhasil diproses. ' . ($discrepancyCount > 0 ? "Terdapat $discrepancyCount selisih yang perlu ditindaklanjuti." : ""));
    }

    public function approve(StockOpname $stockOpname)
    {
        if ($stockOpname->status !== 'pending' && $stockOpname->status !== 'waiting_approval') {
            return back()->with('error', 'Status tidak valid.');
        }

        return DB::transaction(function () use ($stockOpname) {
            $totalDiscrepancyValue = 0;
            foreach ($stockOpname->items as $item) {
                $diff = abs($item->physical_quantity - $item->recorded_quantity);
                $totalDiscrepancyValue += $diff * ($item->product->cost_price ?? 0);
            }

            // FASE 7 Point 3: Threshold Persetujuan (1 Juta)
            $requiresHigherApproval = $totalDiscrepancyValue > 1000000;
            
            if ($stockOpname->status === 'pending') {
                $stockOpname->update([
                    'status' => $requiresHigherApproval ? 'waiting_director_approval' : 'waiting_approval',
                    'approved_by' => null 
                ]);
                
                return redirect()->route('stock_opnames.index')
                    ->with('warning', "Nilai selisih Rp " . number_format($totalDiscrepancyValue) . ". Membutuhkan persetujuan " . ($requiresHigherApproval ? 'Direktur' : 'Manajer') . ".");
            }

            // FINALIZATION: Jika sudah di-approve
            $totalGain = 0;
            $totalLoss = 0;

            foreach ($stockOpname->items as $item) {
                $inventory = Inventory::where('warehouse_id', $stockOpname->warehouse_id)
                    ->where('product_id', $item->product_id)
                    ->first();

                if ($inventory) {
                    $diff = $item->physical_quantity - $item->recorded_quantity;
                    $value = abs($diff) * ($item->product->cost_price ?? 0);
                    
                    if ($diff > 0) $totalGain += $value;
                    else $totalLoss += $value;

                    // 1. Update Physical Stock
                    $inventory->update([
                        'quantity'        => $item->physical_quantity,
                        'is_frozen'       => false, // UNFREEZE
                        'last_counted_at' => now()
                    ]);

                    // 2. Process Pending Adjustments (Transactions during freeze)
                    $pendingAdjustments = \App\Models\PendingStockAdjustment::where('inventory_id', $inventory->id)->get();
                    foreach ($pendingAdjustments as $adj) {
                        $inventory->decrement('quantity', abs($adj->quantity_change));
                        $adj->delete();
                    }
                }
            }

            // 3. Record Accounting Journal
            $this->recordAggregatedJournal($stockOpname, $totalGain, $totalLoss);

            $stockOpname->update(['status' => 'completed', 'approved_by' => auth()->id()]);

            return redirect()->route('stock_opnames.index')->with('success', 'Stock Opname berhasil difinalisasi dan stok telah diperbarui.');
        });
    }

    private function recordAggregatedJournal($opname, $totalGain, $totalLoss)
    {
        $entry = JournalEntry::create([
            'entry_date'  => now()->format('Y-m-d'),
            'reference'   => $opname->opname_number,
            'description' => "Penyesuaian Stok (Agregat) - " . $opname->opname_number,
            'user_id'     => auth()->id() ?? 1,
        ]);

        $inventoryAccount  = Account::where('code', '1201')->first();
        $adjustmentAccount = Account::where('code', '5201')->first();

        if (!$inventoryAccount || !$adjustmentAccount) return;

        // Process Losses (Debit Expense, Credit Inventory)
        if ($totalLoss > 0) {
            $entry->lines()->create(['account_id' => $adjustmentAccount->id, 'debit' => $totalLoss, 'credit' => 0]);
            $entry->lines()->create(['account_id' => $inventoryAccount->id, 'debit' => 0, 'credit' => $totalLoss]);
        }

        // Process Gains (Debit Inventory, Credit Expense/Gain)
        if ($totalGain > 0) {
            $entry->lines()->create(['account_id' => $inventoryAccount->id, 'debit' => $totalGain, 'credit' => 0]);
            $entry->lines()->create(['account_id' => $adjustmentAccount->id, 'debit' => 0, 'credit' => $totalGain]);
        }
    }
}

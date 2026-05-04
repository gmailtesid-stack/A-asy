<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\InventoryLog;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    /**
     * Calculate COGS based on FIFO method and update remaining_quantity in logs.
     * This is called when stock goes OUT.
     */
    public function calculateCogsAndDeduct(Inventory $inventory, int $quantityOut): float
    {
        // FASE 7: Stock Freezing Logic
        if ($inventory->is_frozen) {
            \App\Models\PendingStockAdjustment::create([
                'inventory_id'    => $inventory->id,
                'quantity_change' => -abs($quantityOut),
                'reference_type'  => 'Transaction',
                'reference_id'    => request()->input('transaction_id') ?? 0,
            ]);
            
            // Return current average cost without deducting physical stock
            return $quantityOut * ($inventory->product->cost_price ?? 0);
        }

        return DB::transaction(function () use ($inventory, $quantityOut) {
            // 1. Deduct from batches (FIFO)
            $totalCost = 0;
            $remainingToDeduct = abs($quantityOut);

            // Find 'in' logs with remaining quantity, oldest first (FIFO)
            // LEVEL 10: Use lockForUpdate to prevent race conditions in high-traffic POS
            $batches = InventoryLog::where('inventory_id', $inventory->id)
                ->where('type', 'in')
                ->where('remaining_quantity', '>', 0)
                ->orderBy('created_at', 'asc')
                ->lockForUpdate()
                ->get();
            
            foreach ($batches as $batch) {
                if ($remainingToDeduct <= 0) break;

                $oldVersion = $batch->version;
                $deductFromThisBatch = min($batch->remaining_quantity, $remainingToDeduct);
                $totalCost += $deductFromThisBatch * ($batch->cost_price ?? 0);
                
                // Optimistic Update: ensure version matches
                $affected = InventoryLog::where('id', $batch->id)
                    ->where('version', $oldVersion)
                    ->update([
                        'remaining_quantity' => $batch->remaining_quantity - $deductFromThisBatch,
                        'version' => $oldVersion + 1
                    ]);

                if ($affected === 0) {
                    throw new \Exception("Data stok telah berubah oleh pengguna lain. Silakan coba lagi. (Concurrency Error)");
                }

                $remainingToDeduct -= $deductFromThisBatch;
            }

            // 2. Physical Stock Deduction (Atomic)
            $before = $inventory->quantity;
            $inventory->decrement('quantity', abs($quantityOut));
            $inventory->refresh();
            $after = $inventory->quantity;

            // 3. Fallback for cost
            if ($remainingToDeduct > 0) {
                $totalCost += $remainingToDeduct * ($inventory->product->cost_price ?? 0);
            }

            // 4. Dispatch Event for Logging (Atomic Audit Trail)
            // LEVEL 10: Dispatching here ensures the log is part of the same transaction
            event(new \App\Events\InventoryMoved(
                $inventory, 
                -abs($quantityOut), 
                'out', 
                'POS-TX', // Reference
                $quantityOut > 0 ? ($totalCost / $quantityOut) : 0,
                false, // updatePhysical = false (already done above)
                $before,
                $after
            ));

            return $totalCost;
        });
    }

    /**
     * Initialize remaining_quantity for new 'in' movements.
     */
    public function handleIncomingStock(InventoryLog $log)
    {
        if ($log->type === 'in' && $log->quantity_change > 0) {
            $log->update(['remaining_quantity' => $log->quantity_change]);
        }
    }
}

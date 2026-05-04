<?php

namespace App\Listeners;

use App\Events\TransactionCreated;
use App\Models\Inventory;
use App\Models\InventoryLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class ProcessInventoryReduction implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(TransactionCreated $event): void
    {
        $transaction = $event->transaction;
        $transaction->load('details');

        foreach ($transaction->details as $detail) {
            $inventory = Inventory::where('outlet_id', $transaction->outlet_id)
                ->where('product_id', $detail->product_id)
                ->first();

            if ($inventory) {
                // Log and Check low stock
                // Deduction is now handled ATOMICALLY in TransactionController + InventoryService
                if ($inventory->isLowStock()) {
                    Log::warning("Stok menipis untuk produk {$detail->product_name} (ID: {$detail->product_id}) di Outlet ID: {$transaction->outlet_id}");
                    
                    // You could trigger Email/WhatsApp notification here
                }
            }
        }
    }
}

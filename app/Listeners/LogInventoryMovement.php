<?php

namespace App\Listeners;

use App\Events\InventoryMoved;
use App\Models\InventoryLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogInventoryMovement implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(InventoryMoved $event): void
    {
        $inventory = $event->inventory;
        $change = $event->quantityChange;

        // 1. Perform actual stock update IF requested (default true)
        // If false, it means the Service already did the work (Atomic Mode)
        if ($event->updatePhysical) {
            $before = $inventory->quantity;
            $inventory->increment('quantity', $change);
            $inventory->refresh();
            $after = $inventory->quantity;
        } else {
            // Service already updated, use provided snapshots
            $before = $event->before ?? $inventory->quantity - $change;
            $after  = $event->after  ?? $inventory->quantity;
        }
        
        // 2. Record Log (Audit Trail)
        InventoryLog::create([
            'inventory_id'    => $inventory->id,
            'user_id'         => auth()->id() ?? 1,
            'type'            => $event->type,
            'quantity_before' => $before,
            'quantity_change' => $change,
            'quantity_after'  => $after,
            'reference'       => $event->reference,
            'cost_price'      => $event->costPrice,
            'remaining_quantity' => ($event->type === 'in' && $change > 0) ? $change : 0,
            'notes'           => 'ERP Event Processor',
        ]);
    }
}

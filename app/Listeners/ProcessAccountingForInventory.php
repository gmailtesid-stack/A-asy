<?php

namespace App\Listeners;

use App\Events\InventoryMoved;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ProcessAccountingForInventory
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(InventoryMoved $event): void
    {
        $inventory = $event->inventory;
        $quantity  = $event->quantityChange;
        $type      = $event->type;
        $reference = $event->reference;

        // Only handle GRN for now (Inbound Procurement)
        if (str_starts_with($reference, 'GRN-')) {
            $this->handleGrnAccounting($inventory, $quantity, $reference);
        }
    }

    private function handleGrnAccounting($inventory, $quantity, $reference)
    {
        $product = $inventory->product;
        $totalCost = $quantity * ($product->cost_price ?? 0);

        if ($totalCost <= 0) return;

        \DB::transaction(function () use ($inventory, $totalCost, $reference) {
            $entry = \App\Models\JournalEntry::create([
                'entry_date'  => now()->format('Y-m-d'),
                'reference'   => $reference,
                'description' => "Penerimaan Barang (GRN) - {$reference}",
                'user_id'     => auth()->id() ?? 1,
            ]);

            $inventoryAccount = \App\Models\Account::where('code', '1201')->first(); // Persediaan
            $apAccount        = \App\Models\Account::where('code', '2101')->first(); // Hutang Dagang

            if ($inventoryAccount && $apAccount) {
                // DEBIT INVENTORY ASSET
                $entry->lines()->create([
                    'account_id' => $inventoryAccount->id,
                    'debit'      => $totalCost,
                    'credit'     => 0,
                ]);

                // CREDIT ACCOUNTS PAYABLE
                $entry->lines()->create([
                    'account_id' => $apAccount->id,
                    'debit'      => 0,
                    'credit'     => $totalCost,
                ]);
            }
        });
    }
}

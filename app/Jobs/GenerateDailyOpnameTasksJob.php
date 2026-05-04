<?php

namespace App\Jobs;

use App\Models\Inventory;
use App\Models\StockOpname;
use App\Models\StockOpnameItem;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class GenerateDailyOpnameTasksJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        // Define frequency (days)
        $freq = ['A' => 7, 'B' => 30, 'C' => 90];

        // Group by warehouse to create opname documents
        $warehouses = Inventory::select('warehouse_id')->groupBy('warehouse_id')->get();

        foreach ($warehouses as $wh) {
            $itemsToCount = Inventory::where('warehouse_id', $wh->warehouse_id)
                ->where(function ($query) use ($freq) {
                    $query->whereRaw("DATEDIFF(NOW(), COALESCE(last_counted_at, '2000-01-01')) >= 
                        CASE abc_category 
                            WHEN 'A' THEN {$freq['A']} 
                            WHEN 'B' THEN {$freq['B']} 
                            ELSE {$freq['C']} 
                        END");
                })
                ->limit(20) // Limit per day to keep it "Quick 15-min task"
                ->get();

            if ($itemsToCount->isEmpty()) continue;

            // Create Opname Document
            $opname = StockOpname::create([
                'warehouse_id'  => $wh->warehouse_id,
                'user_id'       => 1, // System or Manager
                'opname_number' => 'AUTO-' . strtoupper(Str::random(8)),
                'status'        => 'pending',
                'type'          => 'daily',
                'is_blind'      => true,
                'notes'         => 'Auto-generated Cycle Counting task.',
            ]);

            foreach ($itemsToCount as $inv) {
                StockOpnameItem::create([
                    'stock_opname_id'   => $opname->id,
                    'product_id'        => $inv->product_id,
                    'recorded_quantity' => $inv->quantity,
                    'physical_quantity' => 0,
                    'verification_status' => 'pending',
                ]);
            }
        }
    }
}

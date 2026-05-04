<?php

namespace App\Jobs;

use App\Models\Inventory;
use App\Models\TransactionDetail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class CalculateAbcCategoryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        // 1. Calculate sales value per product in the last 30 days
        $sales = TransactionDetail::select('product_id', DB::raw('SUM(quantity * price) as total_value'))
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('product_id')
            ->orderBy('total_value', 'desc')
            ->get();

        $totalSalesValue = $sales->sum('total_value');
        if ($totalSalesValue <= 0) return;

        $cumulativeValue = 0;
        foreach ($sales as $sale) {
            $cumulativeValue += $sale->total_value;
            $percentage = ($cumulativeValue / $totalSalesValue) * 100;

            $category = 'C';
            if ($percentage <= 20) {
                $category = 'A';
            } elseif ($percentage <= 50) {
                $category = 'B';
            }

            // Update all inventory records for this product across all outlets
            Inventory::where('product_id', $sale->product_id)
                ->update(['abc_category' => $category]);
        }
    }
}

<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessSyncHpp implements ShouldQueue
{
    use Queueable;

    public array $items;
    public int $outletId;
    public int $userId;

    /**
     * Create a new job instance.
     */
    public function __construct(array $items, int $outletId, int $userId)
    {
        $this->items = $items;
        $this->outletId = $outletId;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $rows = array_map(fn($item) => [
            'outlet_id'      => $this->outletId,
            'user_id'        => $this->userId,
            'invoice_number' => 'HPP-' . strtoupper(\Illuminate\Support\Str::random(6)),
            'subtotal'       => ($item['price'] ?? 15000) * ($item['qty'] ?? 1),
            'total'          => ($item['price'] ?? 15000) * ($item['qty'] ?? 1),
            'status'         => 'completed',
            'created_at'     => now(),
            'updated_at'     => now(),
        ], array_slice($this->items, 0, 100)); // Batasi batch size agar tidak timeout

        if (!empty($rows)) {
            \Illuminate\Support\Facades\DB::table('transactions')->insert($rows);
        }
    }
}

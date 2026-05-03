<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SyncMarketplaceJob implements ShouldQueue
{
    use Queueable;

    protected $channel;

    /**
     * Create a new job instance.
     */
    public function __construct(\App\Models\Channel $channel)
    {
        $this->channel = $channel;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // 1. Initialize API Client based on Channel Type
        // e.g. $client = MarketplaceFactory::make($this->channel);

        \Log::info("Starting sync for channel: {$this->channel->name}");

        try {
            // 2. Sync Stock (OMS -> Marketplace)
            // $this->syncInventory($client);

            // 3. Sync Orders (Marketplace -> OMS)
            // $this->syncOrders($client);

            $this->channel->update([
                'last_sync_at' => now(),
                'sync_status'  => 'success'
            ]);

            \Log::info("Sync completed for channel: {$this->channel->name}");
        } catch (\Exception $e) {
            $this->channel->update(['sync_status' => 'failed']);
            \Log::error("Sync failed for channel {$this->channel->name}: " . $e->getMessage());
            
            // Re-throw to trigger retry policy if needed
            throw $e;
        }
    }
}

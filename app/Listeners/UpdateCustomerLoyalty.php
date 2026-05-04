<?php

namespace App\Listeners;

use App\Events\TransactionCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class UpdateCustomerLoyalty implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(TransactionCreated $event): void
    {
        $transaction = $event->transaction;

        if (!$transaction->customer_id) {
            return;
        }

        try {
            $customer = $transaction->customer;
            
            // Basic Loyalty Logic: 1 point per 10,000 IDR spent
            $pointsToAdd = floor($transaction->total / 10000);
            
            if ($pointsToAdd > 0) {
                $customer->increment('loyalty_points', $pointsToAdd);

                // Tier Upgrade Logic
                $newTier = 'bronze';
                if ($customer->loyalty_points >= 10000) {
                    $newTier = 'vip';
                } elseif ($customer->loyalty_points >= 5000) {
                    $newTier = 'gold';
                } elseif ($customer->loyalty_points >= 1000) {
                    $newTier = 'silver';
                }

                if ($customer->tier !== $newTier) {
                    $customer->update(['tier' => $newTier]);
                    // Trigger notification or audit here if needed
                }
                
                Log::info('LOYALTY UPDATED', [
                    'customer' => $customer->name,
                    'points_added' => $pointsToAdd,
                    'total_points' => $customer->loyalty_points
                ]);
            }
        } catch (\Exception $e) {
            Log::error('LOYALTY UPDATE FAILED: ' . $e->getMessage());
        }
    }
}

<?php

namespace App\Listeners;

use App\Events\TransactionCreated;
use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\JournalLine;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

class RecordAccountingJournal implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(TransactionCreated $event): void
    {
        $transaction = $event->transaction;

        DB::transaction(function () use ($transaction) {
            // 1. Create Journal Entry Header
            $entry = JournalEntry::create([
                'entry_date'  => $transaction->created_at->format('Y-m-d'),
                'reference'   => $transaction->invoice_number,
                'description' => "Penjualan POS - {$transaction->invoice_number}",
                'user_id'     => $transaction->user_id,
            ]);

            // 2. Determine Debit Account (Cash or Bank)
            $debitAccountCode = in_array($transaction->payment_method, ['cash']) ? '1101' : '1102';
            $debitAccount = Account::where('code', $debitAccountCode)->first();

            if (!$debitAccount) return;

            // 3. Journal Lines: DEBIT Cash/Bank
            $entry->lines()->create([
                'account_id' => $debitAccount->id,
                'debit'      => $transaction->total,
                'credit'     => 0,
            ]);

            // 4. Journal Lines: CREDIT Revenue (Gross)
            $revenueAccount = Account::where('code', '4101')->first();
            if ($revenueAccount) {
                $entry->lines()->create([
                    'account_id' => $revenueAccount->id,
                    'debit'      => 0,
                    'credit'     => $transaction->subtotal,
                ]);
            }

            // 5. Journal Lines: DEBIT Sales Discount (if any)
            if ($transaction->discount > 0) {
                $discountAccount = Account::where('code', '4102')->first();
                if ($discountAccount) {
                    $entry->lines()->create([
                        'account_id' => $discountAccount->id,
                        'debit'      => $transaction->discount,
                        'credit'     => 0,
                    ]);
                }
            }

            // 5. Journal Lines: CREDIT Tax (if any)
            if ($transaction->tax > 0) {
                $taxAccount = Account::where('code', '2201')->first();
                if ($taxAccount) {
                    $entry->lines()->create([
                        'account_id' => $taxAccount->id,
                        'debit'      => 0,
                        'credit'     => $transaction->tax,
                    ]);
                }
            }

            // 6. Journal Lines: HPP (Debit COGS, Credit Inventory)
            $totalCost = $transaction->details->sum(function($d) {
                return $d->quantity * ($d->cost_price ?? 0);
            });

            if ($totalCost > 0) {
                $hppAccount = Account::where('code', '5101')->first();
                $inventoryAccount = Account::where('code', '1201')->first();

                if ($hppAccount && $inventoryAccount) {
                    // DEBIT HPP
                    $entry->lines()->create([
                        'account_id' => $hppAccount->id,
                        'debit'      => $totalCost,
                        'credit'     => 0,
                    ]);
                    // CREDIT Inventory
                    $entry->lines()->create([
                        'account_id' => $inventoryAccount->id,
                        'debit'      => 0,
                        'credit'     => $totalCost,
                    ]);
                }
            }
        });
    }
}

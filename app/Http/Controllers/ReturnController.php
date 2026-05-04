<?php

namespace App\Http\Controllers;

use App\Models\ReturnModel;
use App\Models\ReturnItem;
use App\Models\Transaction;
use App\Models\Inventory;
use App\Models\JournalEntry;
use App\Models\JournalLine;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReturnController extends Controller
{
    /**
     * Process a return (RMA) for a given transaction.
     */
    public function store(Request $request)
    {
        $request->validate([
            'transaction_id'       => 'required|exists:transactions,id',
            'reason'               => 'required|string|max:500',
            'refund_method'        => 'required|in:cash,store_credit,exchange',
            'items'                => 'required|array|min:1',
            'items.*.product_id'   => 'required|exists:products,id',
            'items.*.quantity'     => 'required|integer|min:1',
            'items.*.condition'    => 'required|in:good,reject,service',
        ]);

        $transaction = Transaction::with('details')->findOrFail($request->transaction_id);
        $outletId = auth()->user()->outlet_id;

        return DB::transaction(function () use ($request, $transaction, $outletId) {
            $totalRefund = 0;
            
            // 1. Create Return Header
            $returnModel = ReturnModel::create([
                'return_number'  => 'RMA-' . $outletId . '-' . time(),
                'transaction_id' => $transaction->id,
                'customer_id'    => $transaction->customer_id,
                'outlet_id'      => $outletId,
                'processed_by'   => auth()->id(),
                'refund_method'  => $request->refund_method,
                'reason'         => $request->reason,
                'total_refund'   => 0, // Will be updated
            ]);

            // 2. Process Items
            foreach ($request->items as $itemReq) {
                // Find original price sold
                $detail = $transaction->details->where('product_id', $itemReq['product_id'])->first();
                if (!$detail || $itemReq['quantity'] > $detail->quantity) {
                    throw new \Exception("Kuantitas retur melebihi yang dibeli.");
                }

                $refundAmount = $itemReq['quantity'] * ($detail->unit_price - ($detail->discount / $detail->quantity));
                $totalRefund += $refundAmount;

                ReturnItem::create([
                    'return_id'     => $returnModel->id,
                    'product_id'    => $itemReq['product_id'],
                    'quantity'      => $itemReq['quantity'],
                    'refund_amount' => $refundAmount,
                    'condition'     => $itemReq['condition'],
                ]);

                // 3. Reverse Logistics Logic
                if ($itemReq['condition'] === 'good') {
                    // Return to active inventory
                    $inventory = Inventory::where('outlet_id', $outletId)
                        ->where('product_id', $itemReq['product_id'])
                        ->first();
                    
                    if ($inventory) {
                        $inventory->increment('quantity', $itemReq['quantity']);
                        event(new \App\Events\InventoryMoved(
                            $inventory, $itemReq['quantity'], 'in', 'RMA-'.$returnModel->id, $detail->cost_price
                        ));
                    }
                }
                // If reject or service, we don't put it back to active POS inventory.
                // In a full WMS, we would put it in a Quarantine Warehouse.
            }

            $returnModel->update(['total_refund' => $totalRefund]);

            // 4. Refund Financial Logic
            if ($request->refund_method === 'store_credit' && $transaction->customer_id) {
                $transaction->customer->increment('store_credit', $totalRefund);
            }

            // 5. Accounting Reverse Journal
            $this->createReturnJournal($returnModel);

            return response()->json([
                'success' => true,
                'message' => 'Retur berhasil diproses.',
                'rma'     => $returnModel->return_number
            ]);
        });
    }

    private function createReturnJournal(ReturnModel $rma)
    {
        $salesReturnAccount = Account::where('code', '4101')->first(); // Retur Penjualan (Debit)
        $cashAccount        = Account::where('code', '1100')->first(); // Kas (Credit)
        $payableAccount     = Account::where('code', '2100')->first(); // Hutang Store Credit (Credit)

        if ($salesReturnAccount) {
            $journal = JournalEntry::create([
                'reference'   => $rma->return_number,
                'description' => "Retur Penjualan Transaksi #{$rma->transaction_id}",
                'date'        => now(),
                'type'        => 'return',
            ]);

            JournalLine::create(['journal_entry_id' => $journal->id, 'account_id' => $salesReturnAccount->id, 'debit' => $rma->total_refund, 'credit' => 0]);

            $creditAccount = ($rma->refund_method === 'store_credit' && $payableAccount) ? $payableAccount : $cashAccount;
            
            if ($creditAccount) {
                JournalLine::create(['journal_entry_id' => $journal->id, 'account_id' => $creditAccount->id, 'debit' => 0, 'credit' => $rma->total_refund]);
            }
        }
    }
}

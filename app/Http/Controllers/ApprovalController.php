<?php

namespace App\Http\Controllers;

use App\Models\Approval;
use App\Models\StockOpname;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApprovalController extends Controller
{
    /**
     * Dashboard: list all pending approvals for this manager.
     */
    public function index()
    {
        $pending = Approval::with(['requester', 'approvable'])
            ->pending()
            ->latest()
            ->paginate(20);

        return view('admin.approvals.index', compact('pending'));
    }

    /**
     * Approve any pending request (polymorphic).
     */
    public function approve(Request $request, Approval $approval)
    {
        $request->validate(['notes' => 'nullable|string|max:500']);

        DB::transaction(function () use ($approval, $request) {
            $approval->approve(auth()->user(), $request->notes);

            // Execute the side-effect based on the approvable type
            $this->executeApproval($approval);
        });

        return back()->with('success', 'Permintaan disetujui dan perubahan telah diterapkan.');
    }

    /**
     * Reject a pending request.
     */
    public function reject(Request $request, Approval $approval)
    {
        $request->validate(['notes' => 'required|string|max:500']);
        $approval->reject(auth()->user(), $request->notes);

        return back()->with('success', 'Permintaan ditolak.');
    }

    // ── Private: Execute side-effects based on approvable type ─────

    private function executeApproval(Approval $approval): void
    {
        $model = $approval->approvable;

        match (get_class($model)) {
            StockOpname::class => $this->applyStockOpname($model),
            default            => null,
        };
    }

    /**
     * Commits stock opname result to inventory after manager approval.
     */
    private function applyStockOpname(StockOpname $opname): void
    {
        $opname->load('items.inventory');

        foreach ($opname->items as $item) {
            if ($item->inventory) {
                $delta = $item->actual_quantity - $item->system_quantity;
                if ($delta !== 0) {
                    $item->inventory->increment('quantity', $delta);
                    event(new \App\Events\InventoryMoved(
                        $item->inventory,
                        $delta,
                        $delta > 0 ? 'adjustment_in' : 'adjustment_out',
                        "OPNAME-{$opname->id}",
                        0,
                        false,
                        $item->system_quantity,
                        $item->actual_quantity
                    ));
                }
            }
        }

        $opname->update(['status' => 'approved']);
    }
}

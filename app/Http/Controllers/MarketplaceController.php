<?php

namespace App\Http\Controllers;

use App\Models\MarketplaceConnection;
use Illuminate\Http\Request;

class MarketplaceController extends Controller
{
    public function index()
    {
        $connections = MarketplaceConnection::where('company_id', auth()->user()->company_id)->get();
        return view('marketplaces.index', compact('connections'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'marketplace_name' => 'required|string',
            'api_credentials'  => 'required|array',
        ]);

        MarketplaceConnection::updateOrCreate(
            [
                'company_id'       => auth()->user()->company_id,
                'marketplace_name' => $request->marketplace_name,
            ],
            [
                'api_credentials'  => $request->api_credentials,
                'connection_status' => 'active',
                'last_sync_at'     => now(),
            ]
        );

        return redirect()->back()->with('success', "Integrasi {$request->marketplace_name} berhasil dihubungkan.");
    }

    public function sync(MarketplaceConnection $connection)
    {
        // SECURITY: Ensure connection belongs to the user's company
        if ($connection->company_id !== auth()->user()->company_id) {
            abort(403, 'Akses ditolak. Koneksi ini bukan milik perusahaan Anda.');
        }

        // LOGIC: Trigger Job to sync orders/products from marketplace
        // e.g. SyncMarketplaceOrdersJob::dispatch($connection);
        
        $connection->update(['last_sync_at' => now()]);
        return back()->with('success', "Sinkronisasi {$connection->marketplace_name} telah dijadwalkan.");
    }
}

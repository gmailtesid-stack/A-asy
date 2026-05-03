<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use Illuminate\Http\Request;

class ChannelController extends Controller
{
    public function index()
    {
        $channels = Channel::all();
        return view('channels.index', compact('channels'));
    }

    public function sync(Channel $channel)
    {
        if ($channel->status !== 'connected') {
            return back()->with('error', "Kanal {$channel->name} belum terhubung. Silakan hubungkan terlebih dahulu.");
        }

        // Dispatching Background Job for Sync
        \App\Jobs\SyncMarketplaceJob::dispatch($channel);
        
        $channel->update(['sync_status' => 'processing']);

        return back()->with('success', "Proses sinkronisasi untuk {$channel->name} telah dimulai di latar belakang.");
    }

    public function toggleConnection(Channel $channel)
    {
        $newStatus = $channel->status == 'connected' ? 'disconnected' : 'connected';
        $channel->update(['status' => $newStatus]);

        $msg = $newStatus == 'connected' ? "Berhasil menghubungkan ke {$channel->name}!" : "Koneksi ke {$channel->name} diputuskan.";
        return back()->with('success', $msg);
    }
}

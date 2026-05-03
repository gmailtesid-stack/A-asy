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
        // Dispatching Background Job for Sync
        \App\Jobs\SyncMarketplaceJob::dispatch($channel);
        
        $channel->update(['sync_status' => 'processing']);

        return back()->with('success', "Proses sinkronisasi untuk {$channel->name} telah dimulai di latar belakang.");
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ChannelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Channel::insert([
            [
                'name' => 'Shopee Official Store',
                'platform' => 'shopee',
                'status' => 'connected',
                'store_id' => 'SHP-88291',
                'last_sync_at' => now(),
            ],
            [
                'name' => 'Tokopedia Power Merchant',
                'platform' => 'tokopedia',
                'status' => 'connected',
                'store_id' => 'TKP-11202',
                'last_sync_at' => now()->subHours(2),
            ],
            [
                'name' => 'TikTok Shop Global',
                'platform' => 'tiktok',
                'status' => 'disconnected',
                'store_id' => 'TTK-00912',
                'last_sync_at' => null,
            ],
        ]);
    }
}

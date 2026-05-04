<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class MarketplaceConnection extends Model
{
    protected $fillable = [
        'company_id', 
        'marketplace_name', 
        'connection_status', 
        'api_credentials', 
        'last_sync_at'
    ];

    protected $casts = [
        'api_credentials' => 'array',
        'last_sync_at'     => 'datetime',
    ];

    /**
     * Auto-encrypt credentials before saving to database.
     * Uses AES-256-CBC via Laravel's Crypt facade.
     */
    public function setApiCredentialsAttribute($value)
    {
        $this->attributes['api_credentials'] = Crypt::encrypt(json_encode($value));
    }

    /**
     * Auto-decrypt credentials when accessing.
     * Returns empty array if decryption fails (e.g. App Key changed).
     */
    public function getApiCredentialsAttribute($value)
    {
        try {
            return json_decode(Crypt::decrypt($value), true);
        } catch (\Exception $e) {
            Log::error("Failed to decrypt Marketplace API Credentials for ID: {$this->id}. Error: " . $e->getMessage());
            return [];
        }
    }
}

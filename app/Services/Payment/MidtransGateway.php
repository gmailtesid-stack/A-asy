<?php

namespace App\Services\Payment;

use Illuminate\Support\Facades\Log;

class MidtransGateway implements PaymentGatewayInterface
{
    public function createPayment(float $amount, string $referenceId, array $customerData): array
    {
        // Mock Implementation for Phase 3
        Log::info("Mock: Creating Midtrans SNAP token for {$referenceId}");
        
        return [
            'transaction_id' => 'MT-' . uniqid(),
            'payment_url'    => 'https://app.sandbox.midtrans.com/snap/v2/vtweb/mock-token',
            'qr_code'        => null,
            'status'         => 'pending'
        ];
    }

    public function checkStatus(string $transactionId): string
    {
        // Mock Implementation: Simulate checking API
        return 'paid'; 
    }
}

<?php

namespace App\Services\Payment;

interface PaymentGatewayInterface
{
    /**
     * Membuat request pembayaran ke provider.
     * @return array ['payment_url' => '...', 'transaction_id' => '...', 'qr_code' => '...']
     */
    public function createPayment(float $amount, string $referenceId, array $customerData): array;

    /**
     * Memeriksa status pembayaran dari provider.
     * @return string 'paid', 'pending', 'failed', 'expired'
     */
    public function checkStatus(string $transactionId): string;
}

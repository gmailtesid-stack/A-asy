<?php

namespace App\Services\Shipping;

interface ShippingGatewayInterface
{
    /**
     * Meminta tarif ongkir berdasarkan berat dan origin-destination.
     */
    public function checkRates(string $originZip, string $destZip, float $weightKg): array;

    /**
     * Memesan kurir untuk pickup.
     * @return string Nomor Resi (AWB)
     */
    public function requestPickup(string $orderId, array $packageDetails): string;

    /**
     * Melacak status pengiriman.
     */
    public function trackPackage(string $awbNumber): array;
}

<?php

namespace App\Notifications;

use App\Models\Inventory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class LowStockNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private Inventory $inventory,
        private string $productName
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("⚠️ Alert: Stok Menipis — {$this->productName}")
            ->greeting("Halo, {$notifiable->name}!")
            ->line("Stok produk **{$this->productName}** di outlet ID {$this->inventory->outlet_id} menipis.")
            ->line("Sisa stok: **{$this->inventory->quantity}** unit (batas minimum: {$this->inventory->min_quantity})")
            ->action('Kelola Inventori', url('/inventories'))
            ->line('Segera lakukan restok untuk menghindari kehabisan stok.');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'      => 'low_stock',
            'product'   => $this->productName,
            'outlet_id' => $this->inventory->outlet_id,
            'quantity'  => $this->inventory->quantity,
            'minimum'   => $this->inventory->min_quantity,
            'message'   => "Stok {$this->productName} tinggal {$this->inventory->quantity} unit.",
        ];
    }
}

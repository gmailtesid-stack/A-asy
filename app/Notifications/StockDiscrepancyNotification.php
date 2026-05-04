<?php

namespace App\Notifications;

use App\Models\StockOpnameItem;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StockDiscrepancyNotification extends Notification
{
    use Queueable;

    protected $item;

    public function __construct(StockOpnameItem $item)
    {
        $this->item = $item;
    }

    public function via($notifiable): array
    {
        return ['database', 'mail']; // Can add 'whatsapp' via custom driver
    }

    public function toMail($notifiable): MailMessage
    {
        $diff = $this->item->physical_quantity - $this->item->recorded_quantity;
        return (new MailMessage)
            ->error()
            ->subject('Peringatan Selisih Stok: ' . $this->item->product->name)
            ->line('Ditemukan selisih stok saat Opname.')
            ->line('Produk: ' . $this->item->product->name)
            ->line('Sistem: ' . $this->item->recorded_quantity)
            ->line('Fisik: ' . $this->item->physical_quantity)
            ->line('Selisih: ' . $diff)
            ->action('Lihat Detail Opname', url('/stock-opnames/' . $this->item->stock_opname_id));
    }

    public function toArray($notifiable): array
    {
        return [
            'stock_opname_id' => $this->item->stock_opname_id,
            'product_name'    => $this->item->product->name,
            'message'         => "Selisih stok ditemukan pada {$this->item->product->name}. Sistem: {$this->item->recorded_quantity}, Fisik: {$this->item->physical_quantity}",
        ];
    }
}

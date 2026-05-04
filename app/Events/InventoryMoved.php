<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InventoryMoved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public \App\Models\Inventory $inventory;
    public int $quantityChange;
    public string $type;
    public ?string $reference;
    public float $costPrice;
    public bool $updatePhysical;
    public ?int $before;
    public ?int $after;

    /**
     * Create a new event instance.
     */
    public function __construct(\App\Models\Inventory $inventory, int $quantityChange, string $type, ?string $reference = null, float $costPrice = 0, bool $updatePhysical = true, ?int $before = null, ?int $after = null)
    {
        $this->inventory = $inventory;
        $this->quantityChange = $quantityChange;
        $this->type = $type;
        $this->reference = $reference;
        $this->costPrice = $costPrice;
        $this->updatePhysical = $updatePhysical;
        $this->before = $before;
        $this->after = $after;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}

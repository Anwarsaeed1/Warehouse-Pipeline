<?php

namespace App\Events;

use App\Models\Stock;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LowStockDetected implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Stock $stock
    ) {}

    /**
     * Broadcast on the low-stock channel so Reverb can push to connected clients.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('low-stock'),
        ];
    }

    /**
     * Event name for the frontend to listen to.
     */
    public function broadcastAs(): string
    {
        return 'low-stock.detected';
    }

    /**
     * Payload sent over Reverb for real-time low-stock alerts.
     */
    public function broadcastWith(): array
    {
        $this->stock->load(['warehouse', 'inventoryItem']);

        return [
            'stock_id' => $this->stock->id,
            'warehouse_id' => $this->stock->warehouse_id,
            'warehouse_name' => $this->stock->warehouse?->name,
            'inventory_item_id' => $this->stock->inventory_item_id,
            'item_name' => $this->stock->inventoryItem?->name,
            'quantity' => $this->stock->quantity,
            'available_quantity' => $this->stock->available_quantity,
            'low_stock_threshold' => $this->stock->inventoryItem?->low_stock_threshold,
        ];
    }
}

<?php

namespace Tests\Unit;

use App\Events\LowStockDetected;
use App\Listeners\SendLowStockNotification;
use App\Models\InventoryItem;
use App\Models\Stock;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class LowStockDetectedEventTest extends TestCase
{
    use RefreshDatabase;

    public function test_low_stock_detected_event_is_fired_and_queued(): void
    {
        Event::fake([LowStockDetected::class]);

        $warehouse = Warehouse::factory()->create();
        $item = InventoryItem::factory()->create();
        $stock = Stock::create([
            'warehouse_id'      => $warehouse->id,
            'inventory_item_id' => $item->id,
            'quantity'          => 2,
        ]);

        event(new LowStockDetected($stock));

        Event::assertDispatched(LowStockDetected::class, function ($e) use ($stock) {
            return $e->stock->id === $stock->id;
        });

        $listener = new SendLowStockNotification();
        $this->assertInstanceOf(\Illuminate\Contracts\Queue\ShouldQueue::class, $listener);
    }
}

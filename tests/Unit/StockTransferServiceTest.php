<?php

namespace Tests\Unit;

use App\Models\InventoryItem;
use App\Models\Stock;
use App\Models\Warehouse;
use App\Services\Inventory\StockTransferService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockTransferServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_over_transfer_fails(): void
    {
        $warehouseFrom = Warehouse::factory()->create();
        $warehouseTo = Warehouse::factory()->create();
        $item = InventoryItem::factory()->create();
        Stock::create([
            'warehouse_id'      => $warehouseFrom->id,
            'inventory_item_id' => $item->id,
            'quantity'          => 10,
            'reserved_quantity' => 0,
        ]);

        $service = new StockTransferService();

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->expectExceptionMessage(__('api.quantity_exceeds_available', ['available' => 10]));

        $service->transfer([
            'from_warehouse_id'   => $warehouseFrom->id,
            'to_warehouse_id'    => $warehouseTo->id,
            'inventory_item_id'  => $item->id,
            'quantity'           => 15,
        ]);
    }
}

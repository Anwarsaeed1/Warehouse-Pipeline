<?php

namespace Tests\Unit;

use App\Exceptions\InsufficientStockException;
use App\Models\InventoryItem;
use App\Models\Stock;
use App\Models\StockTransfer;
use App\Models\Warehouse;
use App\Services\Inventory\StockTransferService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockTransferServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_transfer_throws_insufficient_stock_when_quantity_exceeds_available(): void
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

        $this->expectException(InsufficientStockException::class);
        $this->expectExceptionMessage(__('api.quantity_exceeds_available', ['available' => 10]));

        $service->transfer([
            'from_warehouse_id'   => $warehouseFrom->id,
            'to_warehouse_id'    => $warehouseTo->id,
            'inventory_item_id'  => $item->id,
            'quantity'           => 15,
        ]);
    }

    public function test_transfer_throws_insufficient_stock_when_reserved_reduces_available(): void
    {
        $warehouseFrom = Warehouse::factory()->create();
        $warehouseTo = Warehouse::factory()->create();
        $item = InventoryItem::factory()->create();
        Stock::create([
            'warehouse_id'      => $warehouseFrom->id,
            'inventory_item_id' => $item->id,
            'quantity'          => 10,
            'reserved_quantity' => 8,
        ]);

        $service = new StockTransferService();

        $this->expectException(InsufficientStockException::class);
        $this->expectExceptionMessage(__('api.quantity_exceeds_available', ['available' => 2]));

        $service->transfer([
            'from_warehouse_id'  => $warehouseFrom->id,
            'to_warehouse_id'    => $warehouseTo->id,
            'inventory_item_id' => $item->id,
            'quantity'          => 5,
        ]);
    }

    public function test_transfer_successfully_decrements_source_and_increments_destination(): void
    {
        $user = \App\Models\User::factory()->create();
        $warehouseFrom = Warehouse::factory()->create();
        $warehouseTo = Warehouse::factory()->create();
        $item = InventoryItem::factory()->create();
        Stock::create([
            'warehouse_id'      => $warehouseFrom->id,
            'inventory_item_id' => $item->id,
            'quantity'          => 20,
            'reserved_quantity' => 0,
        ]);

        $service = new StockTransferService();
        $transfer = $service->transfer([
            'from_warehouse_id'  => $warehouseFrom->id,
            'to_warehouse_id'    => $warehouseTo->id,
            'inventory_item_id' => $item->id,
            'quantity'          => 7,
            'note'              => 'Test note',
        ], $user->id);

        $this->assertInstanceOf(StockTransfer::class, $transfer);
        $this->assertSame($warehouseFrom->id, $transfer->from_warehouse_id);
        $this->assertSame($warehouseTo->id, $transfer->to_warehouse_id);
        $this->assertSame(7, $transfer->quantity);
        $this->assertSame($user->id, $transfer->transferred_by);
        $this->assertStringStartsWith('TRF-', $transfer->transfer_number);

        $this->assertDatabaseHas('stocks', [
            'warehouse_id'      => $warehouseFrom->id,
            'inventory_item_id' => $item->id,
            'quantity'          => 13,
        ]);
        $this->assertDatabaseHas('stocks', [
            'warehouse_id'      => $warehouseTo->id,
            'inventory_item_id' => $item->id,
            'quantity'          => 7,
        ]);
    }

    public function test_transfer_creates_destination_stock_if_not_exists(): void
    {
        $warehouseFrom = Warehouse::factory()->create();
        $warehouseTo = Warehouse::factory()->create();
        $item = InventoryItem::factory()->create();
        Stock::create([
            'warehouse_id'      => $warehouseFrom->id,
            'inventory_item_id' => $item->id,
            'quantity'          => 5,
            'reserved_quantity' => 0,
        ]);

        $this->assertDatabaseMissing('stocks', [
            'warehouse_id'      => $warehouseTo->id,
            'inventory_item_id' => $item->id,
        ]);

        $service = new StockTransferService();
        $service->transfer([
            'from_warehouse_id'  => $warehouseFrom->id,
            'to_warehouse_id'    => $warehouseTo->id,
            'inventory_item_id' => $item->id,
            'quantity'          => 3,
        ], null);

        $this->assertDatabaseHas('stocks', [
            'warehouse_id'      => $warehouseTo->id,
            'inventory_item_id' => $item->id,
            'quantity'          => 3,
        ]);
    }

    public function test_transfer_loads_relations_on_returned_model(): void
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
        $transfer = $service->transfer([
            'from_warehouse_id'  => $warehouseFrom->id,
            'to_warehouse_id'    => $warehouseTo->id,
            'inventory_item_id' => $item->id,
            'quantity'          => 2,
        ], null);

        $this->assertTrue($transfer->relationLoaded('fromWarehouse'));
        $this->assertTrue($transfer->relationLoaded('toWarehouse'));
        $this->assertTrue($transfer->relationLoaded('inventoryItem'));
        $this->assertTrue($transfer->relationLoaded('transferredBy'));
    }
}

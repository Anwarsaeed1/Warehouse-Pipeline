<?php

namespace Tests\Feature;

use App\Models\InventoryItem;
use App\Models\Permission;
use App\Models\Stock;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockTransferTest extends TestCase
{
    use RefreshDatabase;

    public function test_successful_transfer(): void
    {
        $user = User::factory()->create();
        Permission::firstOrCreate(['name' => 'create-stock-transfer', 'guard_name' => 'sanctum']);
        $user->givePermissionTo('create-stock-transfer');

        $warehouseFrom = Warehouse::factory()->create();
        $warehouseTo = Warehouse::factory()->create();
        $item = InventoryItem::factory()->create();
        Stock::create([
            'warehouse_id'      => $warehouseFrom->id,
            'inventory_item_id' => $item->id,
            'quantity'          => 20,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/stock-transfers', [
                'from_warehouse_id'   => $warehouseFrom->id,
                'to_warehouse_id'    => $warehouseTo->id,
                'inventory_item_id'  => $item->id,
                'quantity'           => 5,
            ]);

        $response->assertStatus(201);
        $response->assertJsonPath('data.quantity', 5);
        $response->assertJsonPath('data.from_warehouse_id', $warehouseFrom->id);
        $response->assertJsonPath('data.to_warehouse_id', $warehouseTo->id);

        $this->assertDatabaseHas('stocks', [
            'warehouse_id'      => $warehouseFrom->id,
            'inventory_item_id' => $item->id,
            'quantity'          => 15,
        ]);
        $this->assertDatabaseHas('stocks', [
            'warehouse_id'      => $warehouseTo->id,
            'inventory_item_id' => $item->id,
            'quantity'          => 5,
        ]);
    }
}

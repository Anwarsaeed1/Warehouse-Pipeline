<?php

namespace Tests\Feature;

use App\Models\InventoryItem;
use App\Models\Permission;
use App\Models\Stock;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockTransferApiTest extends TestCase
{
    use RefreshDatabase;

    private function userWithPermission(string $permission): User
    {
        $user = User::factory()->create();
        Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'sanctum']);
        $user->givePermissionTo($permission);
        return $user;
    }

    public function test_stock_transfer_returns_403_without_create_permission(): void
    {
        $user = User::factory()->create();
        $wFrom = Warehouse::factory()->create();
        $wTo = Warehouse::factory()->create();
        $item = InventoryItem::factory()->create();
        Stock::create([
            'warehouse_id' => $wFrom->id,
            'inventory_item_id' => $item->id,
            'quantity' => 20,
        ]);

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/stock-transfers', [
            'from_warehouse_id' => $wFrom->id,
            'to_warehouse_id' => $wTo->id,
            'inventory_item_id' => $item->id,
            'quantity' => 5,
        ]);

        $response->assertStatus(403);
    }

    public function test_stock_transfer_returns_422_when_quantity_exceeds_available(): void
    {
        $user = $this->userWithPermission('create-stock-transfer');
        $wFrom = Warehouse::factory()->create();
        $wTo = Warehouse::factory()->create();
        $item = InventoryItem::factory()->create();
        Stock::create([
            'warehouse_id' => $wFrom->id,
            'inventory_item_id' => $item->id,
            'quantity' => 3,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/stock-transfers', [
                'from_warehouse_id' => $wFrom->id,
                'to_warehouse_id' => $wTo->id,
                'inventory_item_id' => $item->id,
                'quantity' => 10,
            ]);

        $response->assertStatus(422);
        $this->assertArrayHasKey('message', $response->json());
    }

    public function test_stock_transfer_validation_rejects_same_warehouse_and_invalid_quantity(): void
    {
        $user = $this->userWithPermission('create-stock-transfer');
        $w = Warehouse::factory()->create();
        $item = InventoryItem::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/stock-transfers', [
            'from_warehouse_id' => $w->id,
            'to_warehouse_id' => $w->id,
            'inventory_item_id' => $item->id,
            'quantity' => 0,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['to_warehouse_id', 'quantity']);
    }
}

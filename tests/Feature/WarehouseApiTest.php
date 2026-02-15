<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WarehouseApiTest extends TestCase
{
    use RefreshDatabase;

    private function userWithReadWarehouse(): User
    {
        $user = User::factory()->create();
        Permission::firstOrCreate(['name' => 'read-warehouse', 'guard_name' => 'sanctum']);
        $user->givePermissionTo('read-warehouse');

        return $user;
    }

    public function test_warehouses_list_returns_401_without_auth(): void
    {
        $response = $this->getJson('/api/warehouses');

        $response->assertStatus(401);
    }

    public function test_warehouses_list_returns_403_without_permission(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/warehouses');

        $response->assertStatus(403);
    }

    public function test_warehouses_list_returns_paginated_warehouses(): void
    {
        Warehouse::factory()->count(3)->create();
        $user = $this->userWithReadWarehouse();

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/warehouses?pageSize=2');

        $response->assertStatus(200);
        $response->assertJsonPath('status', true);
        $response->assertJsonStructure(['data' => ['data', 'current_page', 'per_page']]);
        $this->assertCount(2, $response->json('data.data'));
    }

    public function test_warehouses_list_can_filter_by_name(): void
    {
        Warehouse::factory()->create(['name' => 'Main Depot']);
        Warehouse::factory()->create(['name' => 'Other Place']);
        $user = $this->userWithReadWarehouse();

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/warehouses?name=Depot');

        $response->assertStatus(200);
        $data = $response->json('data.data');
        $this->assertCount(1, $data);
        $this->assertStringContainsString('Depot', $data[0]['name']);
    }
}

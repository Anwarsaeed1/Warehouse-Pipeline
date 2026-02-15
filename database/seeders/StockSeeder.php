<?php

namespace Database\Seeders;

use App\Models\InventoryItem;
use App\Models\Stock;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class StockSeeder extends Seeder
{
    /**
     * Seed stocks for existing warehouses and inventory items.
     * Run after WarehouseSeeder and InventoryItemSeeder.
     */
    public function run(): void
    {
        $warehouses = Warehouse::query()->orderBy('code')->get();
        $items = InventoryItem::query()->orderBy('sku')->get();

        if ($warehouses->isEmpty() || $items->isEmpty()) {
            return;
        }

        // Main Warehouse (WH0001): good stock for most items, one low for testing
        $main = $warehouses[0];
        foreach ($items as $index => $item) {
            Stock::firstOrCreate(
                [
                    'warehouse_id'      => $main->id,
                    'inventory_item_id' => $item->id,
                ],
                [
                    'quantity'          => $index === 1 ? 3 : 50, // Widget B low stock
                    'reserved_quantity' => 0,
                ]
            );
        }

        // North Warehouse (WH0002): mixed quantities
        $north = $warehouses[1];
        $northQuantities = [30, 20, 8, 40, 4];
        foreach ($items as $index => $item) {
            Stock::firstOrCreate(
                [
                    'warehouse_id'      => $north->id,
                    'inventory_item_id' => $item->id,
                ],
                [
                    'quantity'          => $northQuantities[$index] ?? 0,
                    'reserved_quantity' => 0,
                ]
            );
        }

        // South (WH0003): fewer items
        $south = $warehouses[2];
        $southQuantities = [0 => 25, 1 => 2, 3 => 15];
        foreach ($southQuantities as $itemIndex => $qty) {
            $item = $items[$itemIndex];
            Stock::firstOrCreate(
                [
                    'warehouse_id'      => $south->id,
                    'inventory_item_id' => $item->id,
                ],
                ['quantity' => $qty, 'reserved_quantity' => 0]
            );
        }
    }
}

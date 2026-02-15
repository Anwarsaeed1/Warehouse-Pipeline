<?php

namespace Database\Seeders;

use App\Models\InventoryItem;
use Illuminate\Database\Seeder;

class InventoryItemSeeder extends Seeder
{
    /**
     * Seed inventory items.
     */
    public function run(): void
    {
        $data = [
            ['name' => 'Widget A', 'sku' => 'WDG-001', 'description' => 'Standard widget', 'price' => 9.99, 'low_stock_threshold' => 10],
            ['name' => 'Widget B', 'sku' => 'WDG-002', 'description' => 'Premium widget', 'price' => 19.99, 'low_stock_threshold' => 5],
            ['name' => 'Gadget X', 'sku' => 'GDT-001', 'description' => 'Basic gadget', 'price' => 14.50, 'low_stock_threshold' => 15],
            ['name' => 'Gadget Y', 'sku' => 'GDT-002', 'description' => 'Pro gadget', 'price' => 29.00, 'low_stock_threshold' => 8],
            ['name' => 'Tool Set', 'sku' => 'TLS-001', 'description' => 'Starter tool set', 'price' => 49.99, 'low_stock_threshold' => 5],
        ];

        foreach ($data as $row) {
            InventoryItem::firstOrCreate(
                ['sku' => $row['sku']],
                [
                    'name'                => $row['name'],
                    'description'         => $row['description'],
                    'price'               => $row['price'],
                    'low_stock_threshold' => $row['low_stock_threshold'],
                    'is_active'           => 1,
                ]
            );
        }
    }
}

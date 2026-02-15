<?php

namespace Database\Seeders;

use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    /**
     * Seed warehouses.
     */
    public function run(): void
    {
        $data = [
            ['code' => 'WH0001', 'name' => 'Main Warehouse', 'location' => 'City Center, Building A'],
            ['code' => 'WH0002', 'name' => 'North Warehouse', 'location' => 'Industrial Zone, Block 5'],
            ['code' => 'WH0003', 'name' => 'South Distribution', 'location' => 'Port Area, Dock 2'],
        ];

        foreach ($data as $row) {
            Warehouse::firstOrCreate(
                ['code' => $row['code']],
                [
                    'name'     => $row['name'],
                    'location' => $row['location'],
                    'is_active'=> 1,
                ]
            );
        }
    }
}

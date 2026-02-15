<?php

namespace Database\Factories;

use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class WarehouseFactory extends Factory
{
    protected $model = Warehouse::class;

    public function definition(): array
    {
        static $seq = 0;
        $seq++;

        return [
            'code'     => 'WH' . str_pad((string) $seq, 4, '0', STR_PAD_LEFT),
            'name'     => fake()->company(),
            'location' => fake()->city() . ', ' . fake()->country(),
            'is_active'=> 1,
        ];
    }
}

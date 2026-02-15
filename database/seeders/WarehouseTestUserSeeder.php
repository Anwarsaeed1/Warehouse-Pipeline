<?php

namespace Database\Seeders;

use App\Enum\User\UserGenderEnum;
use App\Models\User;
use Illuminate\Database\Seeder;

class WarehouseTestUserSeeder extends Seeder
{
    /**
     * Seed the test user for warehouse/inventory tasks (admin role).
     */
    public function run(): void
    {
        $user = User::query()->firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name'     => 'Test User',
                'password' => '123456',
                'gender'   => UserGenderEnum::Male->value,
                'is_active'=> true,
            ]
        );

        if (!$user->hasRole('root')) {
            $user->assignRole('root');
        }
    }
}

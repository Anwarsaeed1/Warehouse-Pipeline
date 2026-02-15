<?php

namespace App\Policies\Inventory;

use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Auth\Access\HandlesAuthorization;

class WarehousePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('read-warehouse');
    }

    public function view(User $user, ?Warehouse $warehouse = null): bool
    {
        return $user->can('read-warehouse');
    }

    public function create(User $user): bool
    {
        return $user->can('create-warehouse');
    }

    public function update(User $user, Warehouse $warehouse): bool
    {
        return $user->can('update-warehouse');
    }

    public function delete(User $user, Warehouse $warehouse): bool
    {
        return $user->can('delete-warehouse');
    }
}

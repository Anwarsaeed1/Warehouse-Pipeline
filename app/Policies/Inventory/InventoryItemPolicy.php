<?php

namespace App\Policies\Inventory;

use App\Models\InventoryItem;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class InventoryItemPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('read-inventory-item');
    }

    public function view(User $user, ?InventoryItem $inventoryItem = null): bool
    {
        return $user->can('read-inventory-item');
    }

    public function create(User $user): bool
    {
        return $user->can('create-inventory-item');
    }

    public function update(User $user, InventoryItem $inventoryItem): bool
    {
        return $user->can('update-inventory-item');
    }

    public function delete(User $user, InventoryItem $inventoryItem): bool
    {
        return $user->can('delete-inventory-item');
    }
}

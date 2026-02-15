<?php

namespace App\Policies\Inventory;

use App\Models\Stock;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StockPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('read-stock');
    }

    public function view(User $user, ?Stock $stock = null): bool
    {
        return $user->can('read-stock');
    }

    public function update(User $user, Stock $stock): bool
    {
        return $user->can('update-stock');
    }
}

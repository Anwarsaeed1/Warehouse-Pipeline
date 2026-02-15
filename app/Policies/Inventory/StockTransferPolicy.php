<?php

namespace App\Policies\Inventory;

use App\Models\StockTransfer;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StockTransferPolicy
{
    use HandlesAuthorization;

    public function view(User $user, ?StockTransfer $stockTransfer = null): bool
    {
        return $user->can('read-stock-transfer');
    }

    public function create(User $user): bool
    {
        return $user->can('create-stock-transfer');
    }
}

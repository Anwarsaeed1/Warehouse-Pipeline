<?php

namespace App\Filters\Inventory;

use Closure;

class InventoryItemNameFilter
{
    public function handle($request, Closure $next)
    {
        $query = $next($request);

        when(request('search'), static function () use ($query) {
            $query->whereHas('inventoryItem', fn($q) => $q->where('name', 'like', '%' . request('search') . '%'));
        });

        return $query;
    }
}

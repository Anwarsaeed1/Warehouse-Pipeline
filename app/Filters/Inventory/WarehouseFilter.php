<?php

namespace App\Filters\Inventory;

use Closure;

class WarehouseFilter
{
    public function handle($request, Closure $next)
    {
        $query = $next($request);

        when(request('warehouse_id'), static function () use ($query) {
            $query->where('warehouse_id', request('warehouse_id'));
        });

        return $query;
    }
}

<?php

namespace App\Filters\Inventory;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class WarehouseNameFilter
{
    public function handle($request, Closure $next): Builder
    {
        $query = $next($request);

        if (request()->filled('name')) {
            $query->where('name', 'like', '%' . request('name') . '%');
        }

        return $query;
    }
}

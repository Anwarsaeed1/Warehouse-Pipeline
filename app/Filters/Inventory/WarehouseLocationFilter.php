<?php

namespace App\Filters\Inventory;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class WarehouseLocationFilter
{
    public function handle($request, Closure $next): Builder
    {
        $query = $next($request);

        if (request()->filled('location')) {
            $query->where('location', 'like', '%' . request('location') . '%');
        }

        return $query;
    }
}

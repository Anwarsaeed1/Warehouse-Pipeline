<?php

namespace App\Filters\Inventory;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class WarehouseCodeFilter
{
    public function handle($request, Closure $next): Builder
    {
        $query = $next($request);

        if (request()->filled('code')) {
            $query->where('code', 'like', '%' . request('code') . '%');
        }

        return $query;
    }
}

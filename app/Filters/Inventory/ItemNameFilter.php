<?php

namespace App\Filters\Inventory;

use Closure;

class ItemNameFilter
{
    public function handle($request, Closure $next)
    {
        $query = $next($request);

        when(request('search'), static function () use ($query) {
            $query->where('name', 'like', '%' . request('search') . '%');
        });

        return $query;
    }
}

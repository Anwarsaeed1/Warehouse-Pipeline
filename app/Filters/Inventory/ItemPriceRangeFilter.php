<?php

namespace App\Filters\Inventory;

use Closure;

class ItemPriceRangeFilter
{
    public function handle($request, Closure $next)
    {
        $query = $next($request);

        when(request('price_min'), static function () use ($query) {
            $query->where('price', '>=', request('price_min'));
        });

        when(request('price_max'), static function () use ($query) {
            $query->where('price', '<=', request('price_max'));
        });

        return $query;
    }
}

<?php

namespace App\Filters\Inventory;

use App\Enum\Global\ActiveTypeEnum;
use Closure;
use Illuminate\Database\Eloquent\Builder;

class StockItemIsActiveFilter
{
    public function handle($request, Closure $next): Builder
    {
        $query = $next($request);

        if (request()->has('item_is_active') && request()->item_is_active !== '' && request()->item_is_active !== null) {
            $value = request()->item_is_active;
            $enum = is_numeric($value) ? ActiveTypeEnum::tryFrom((int) $value) : null;
            if ($enum !== null) {
                $query->whereHas('inventoryItem', fn (Builder $q) => $q->where('is_active', $enum));
            }
        }

        return $query;
    }
}

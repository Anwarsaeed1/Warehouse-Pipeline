<?php

namespace App\Filters\Inventory;

use App\Enum\Global\ActiveTypeEnum;
use Closure;
use Illuminate\Database\Eloquent\Builder;

class StockWarehouseIsActiveFilter
{
    public function handle($request, Closure $next): Builder
    {
        $query = $next($request);

        if (request()->has('warehouse_is_active') && request()->warehouse_is_active !== '' && request()->warehouse_is_active !== null) {
            $value = request()->warehouse_is_active;
            $enum = is_numeric($value) ? ActiveTypeEnum::tryFrom((int) $value) : null;
            if ($enum !== null) {
                $query->whereHas('warehouse', fn (Builder $q) => $q->where('is_active', $enum));
            }
        }

        return $query;
    }
}

<?php

namespace App\Filters\Inventory;

use App\Enum\Global\ActiveTypeEnum;
use Closure;
use Illuminate\Database\Eloquent\Builder;

class ItemIsActiveFilter
{
    public function handle($request, Closure $next): Builder
    {
        $query = $next($request);

        if (request()->has('is_active') && request()->is_active !== '' && request()->is_active !== null) {
            $value = request()->is_active;
            $enum = is_numeric($value) ? ActiveTypeEnum::tryFrom((int) $value) : null;
            if ($enum !== null) {
                $query->where('is_active', $enum);
            }
        }

        return $query;
    }
}

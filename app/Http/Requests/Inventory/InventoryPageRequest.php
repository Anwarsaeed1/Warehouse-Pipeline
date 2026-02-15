<?php

namespace App\Http\Requests\Inventory;

use App\Http\Requests\Global\Other\PageRequest;

class InventoryPageRequest extends PageRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'warehouse_id'       => 'nullable|integer|exists:warehouses,id',
            'search'             => 'nullable|string|max:255',
            'price_min'          => 'nullable|numeric|min:0',
            'price_max'          => 'nullable|numeric|min:0|gte:price_min',
            'is_active'          => 'nullable|integer|in:0,1',
            'warehouse_is_active' => 'nullable|integer|in:0,1',
            'item_is_active'     => 'nullable|integer|in:0,1',
        ]);
    }
}

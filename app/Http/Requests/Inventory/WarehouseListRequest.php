<?php

namespace App\Http\Requests\Inventory;

use App\Http\Requests\Global\Other\PageRequest;

class WarehouseListRequest extends PageRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'name'     => 'nullable|string|max:255',
            'code'     => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'is_active'=> 'nullable|integer|in:0,1',
        ]);
    }
}

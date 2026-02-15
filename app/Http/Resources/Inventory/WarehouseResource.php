<?php

namespace App\Http\Resources\Inventory;

use App\Enum\Global\ActiveTypeEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WarehouseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'code'           => $this->code,
            'name'           => $this->name,
            'location'       => $this->location,
            'is_active'      => $this->is_active?->value,
            'is_active_label'=> $this->is_active ? ActiveTypeEnum::resolve($this->is_active->value) : null,
        ];
    }
}

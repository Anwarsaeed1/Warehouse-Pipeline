<?php

namespace App\Http\Resources\Inventory;

use App\Enum\Global\ActiveTypeEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'name'                => $this->name,
            'sku'                 => $this->sku,
            'description'         => $this->description,
            'price'               => $this->price !== null ? (float) $this->price : null,
            'low_stock_threshold' => $this->low_stock_threshold,
            'is_active'           => $this->is_active?->value,
            'is_active_label'     => $this->is_active ? ActiveTypeEnum::resolve($this->is_active->value) : null,
        ];
    }
}

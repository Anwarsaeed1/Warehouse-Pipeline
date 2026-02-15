<?php

namespace App\Http\Resources\Inventory;

use App\Enum\Global\ActiveTypeEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'warehouse_id'      => $this->warehouse_id,
            'warehouse'         => $this->whenLoaded('warehouse', fn() => [
                'id'             => $this->warehouse->id,
                'code'           => $this->warehouse->code,
                'name'           => $this->warehouse->name,
                'location'       => $this->warehouse->location,
                'is_active'      => $this->warehouse->is_active?->value,
                'is_active_label'=> $this->warehouse->is_active ? ActiveTypeEnum::resolve($this->warehouse->is_active->value) : null,
            ]),
            'inventory_item'    => $this->whenLoaded('inventoryItem', fn() => [
                'id'                  => $this->inventoryItem->id,
                'name'                => $this->inventoryItem->name,
                'sku'                 => $this->inventoryItem->sku,
                'description'        => $this->inventoryItem->description,
                'price'              => $this->inventoryItem->price !== null ? (float) $this->inventoryItem->price : null,
                'low_stock_threshold' => $this->inventoryItem->low_stock_threshold,
                'is_active'           => $this->inventoryItem->is_active?->value,
                'is_active_label'     => $this->inventoryItem->is_active ? ActiveTypeEnum::resolve($this->inventoryItem->is_active->value) : null,
            ]),
            'quantity'          => $this->quantity,
            'reserved_quantity' => $this->reserved_quantity,
            'available_quantity'=> $this->available_quantity,
            'inventory_item_id' => $this->inventory_item_id,
        ];
    }
}

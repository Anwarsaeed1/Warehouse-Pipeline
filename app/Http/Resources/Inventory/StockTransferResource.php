<?php

namespace App\Http\Resources\Inventory;

use App\Enum\Inventory\StockTransferStatusEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockTransferResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'transfer_number'     => $this->transfer_number,
            'from_warehouse_id'   => $this->from_warehouse_id,
            'to_warehouse_id'     => $this->to_warehouse_id,
            'inventory_item_id'   => $this->inventory_item_id,
            'from_warehouse'      => $this->whenLoaded('fromWarehouse', fn () => new WarehouseResource($this->fromWarehouse)),
            'to_warehouse'        => $this->whenLoaded('toWarehouse', fn () => new WarehouseResource($this->toWarehouse)),
            'inventory_item'      => $this->whenLoaded('inventoryItem', fn () => new InventoryItemResource($this->inventoryItem)),
            'transferred_by_user' => $this->whenLoaded('transferredBy', fn () => [
                'id'    => $this->transferredBy->id,
                'name'  => $this->transferredBy->name,
                'email' => $this->transferredBy->email,
            ]),
            'quantity'            => $this->quantity,
            'status'              => $this->status?->value,
            'status_label'         => $this->status ? StockTransferStatusEnum::resolve($this->status->value) : null,
            'note'                 => $this->note,
            'completed_at'         => $this->completed_at?->format('c'),
            'transferred_by'       => $this->transferred_by,
            'created_at'           => $this->created_at?->format('c'),
        ];
    }
}

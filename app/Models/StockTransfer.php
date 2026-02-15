<?php

namespace App\Models;

use App\Enum\Inventory\StockTransferStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockTransfer extends Model
{
    public bool $inPermission = true;
    public array $basicOperations = ['create', 'read'];

    protected $fillable = [
        'transfer_number',
        'from_warehouse_id',
        'to_warehouse_id',
        'inventory_item_id',
        'quantity',
        'status',
        'note',
        'completed_at',
        'transferred_by',
    ];

    protected function casts(): array
    {
        return [
            'status'      => StockTransferStatusEnum::class,
            'completed_at' => 'datetime',
        ];
    }

    /**
     * Get the from warehouse of the stock transfer.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function fromWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'from_warehouse_id');
    }

    /**
     * Get the to warehouse of the stock transfer.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function toWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'to_warehouse_id');
    }
    
    /**
     * Get the inventory item associated with the stock transfer.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class);
    }

    /**
     * Get the user who transferred the stock.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function transferredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'transferred_by');
    }

    /**
     * Checks if the stock transfer is pending.
     *
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->status === StockTransferStatusEnum::Pending;
    }

    /**
     * Checks if the stock transfer is completed.
     *
     * @return bool
     */
    public function isCompleted(): bool
    {
        return $this->status === StockTransferStatusEnum::Completed;
    }
}

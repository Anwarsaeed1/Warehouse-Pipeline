<?php

namespace App\Services\Inventory;

use App\Enum\Inventory\StockTransferStatusEnum;
use App\Events\LowStockDetected;
use App\Models\InventoryItem;
use App\Models\Stock;
use App\Models\StockTransfer;
use App\Exceptions\InsufficientStockException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

class StockTransferService
{
    private const LOW_STOCK_THRESHOLD_CONFIG_KEY = 'inventory.low_stock_threshold';
    private const DEFAULT_LOW_STOCK_THRESHOLD = 10;

    /**
     * Transfer stock from one warehouse to another.
     *
     * @param array $validated
     * @param int|null $userId
     * @return StockTransfer
     *
     * @throws InsufficientStockException
     * @throws HttpException
     */
    public function transfer(array $validated, ?int $userId = null): StockTransfer
    {

        return DB::transaction(function () use ($validated, $userId) {
            $fromStock = $this->lockSourceStock($validated);
            
            $this->validateAvailableQuantity($fromStock, $validated['quantity']);
            
            $fromStock->decrement('quantity', $validated['quantity']);
            
            $toStock = $this->getOrCreateDestinationStock($validated);
            $toStock->increment('quantity', $validated['quantity']);
            
            $transfer = $this->createTransferRecord($validated, $userId);
            
            $this->invalidateWarehouseCaches(
                $validated['from_warehouse_id'], 
                $validated['to_warehouse_id']
            );
            
            $this->dispatchLowStockEventsAfterCommit($fromStock, $toStock);
            
            return $this->loadTransferRelations($transfer);
        });
    }

    /**
     * Lock the source stock record for update.
     *
     * @param array $validated
     * @return Stock
     *
     * @throws HttpException
     */
    private function lockSourceStock(array $validated): Stock
    {
        return Stock::query()
            ->where('warehouse_id', $validated['from_warehouse_id'])
            ->where('inventory_item_id', $validated['inventory_item_id'])
            ->lockForUpdate()
            ->firstOrFail();
    }

    /**
     * Validate if requested quantity is available.
     *
     * @param Stock $stock
     * @param int $requestedQuantity
     * @return void
     *
     * @throws InsufficientStockException
     */
    private function validateAvailableQuantity(Stock $stock, int $requestedQuantity): void
    {
        $availableQuantity = $stock->quantity - $stock->reserved_quantity;
        
        if ($availableQuantity < $requestedQuantity) {
            throw new InsufficientStockException(
                __(
                    'api.quantity_exceeds_available', 
                    ['available' => $availableQuantity]
                )
            );
        }
    }

    /**
     * Get or create destination stock record.
     *
     * @param array $validated
     * @return Stock
     */
    private function getOrCreateDestinationStock(array $validated): Stock
    {
        return Stock::firstOrCreate(
            [
                'warehouse_id' => $validated['to_warehouse_id'],
                'inventory_item_id' => $validated['inventory_item_id'],
            ],
            [
                'quantity' => 0, 
                'reserved_quantity' => 0
            ]
        );
    }

    /**
     * Create the transfer record.
     *
     * @param array $validated
     * @param int|null $userId
     * @return StockTransfer
     */
    private function createTransferRecord(array $validated, ?int $userId): StockTransfer
    {
        return StockTransfer::create([
            'transfer_number' => generateTransferNumber(),
            'from_warehouse_id' => $validated['from_warehouse_id'],
            'to_warehouse_id' => $validated['to_warehouse_id'],
            'inventory_item_id' => $validated['inventory_item_id'],
            'quantity' => $validated['quantity'],
            'status' => StockTransferStatusEnum::Completed,
            'note' => $validated['note'] ?? null,
            'completed_at' => now(),
            'transferred_by' => $userId,
        ]);
    }

    /**
     * Invalidate warehouse inventory caches.
     *
     * @param int $fromWarehouseId
     * @param int $toWarehouseId
     * @return void
     */
    private function invalidateWarehouseCaches(int $fromWarehouseId, int $toWarehouseId): void
    {
        Cache::increment("warehouse.inventory.{$fromWarehouseId}.version");
        Cache::increment("warehouse.inventory.{$toWarehouseId}.version");
    }

    /**
     * Dispatch low stock events after transaction commit.
     *
     * @param Stock $fromStock
     * @param Stock $toStock
     * @return void
     */
    private function dispatchLowStockEventsAfterCommit(Stock $fromStock, Stock $toStock): void
    {
        DB::afterCommit(function () use ($fromStock, $toStock) {
            $threshold = $this->getLowStockThreshold($fromStock->inventory_item_id);
            
            $this->dispatchLowStockEventIfNeeded($fromStock, $threshold);
            $this->dispatchLowStockEventIfNeeded($toStock, $threshold);
        });
    }

    /**
     * Get low stock threshold for an inventory item.
     *
     * @param int $inventoryItemId
     * @return int
     */
    private function getLowStockThreshold(int $inventoryItemId): int
    {
        $inventoryItem = InventoryItem::find($inventoryItemId);
        
        return $inventoryItem?->low_stock_threshold 
            ?? config(self::LOW_STOCK_THRESHOLD_CONFIG_KEY, self::DEFAULT_LOW_STOCK_THRESHOLD);
    }

    /**
     * Dispatch low stock event if quantity is below threshold.
     *
     * @param Stock $stock
     * @param int $threshold
     * @return void
     */
    private function dispatchLowStockEventIfNeeded(Stock $stock, int $threshold): void
    {
        $stock->refresh();
        
        if ($stock->quantity <= $threshold) {
            event(new LowStockDetected($stock));
        }
    }

    /**
     * Load transfer relationships.
     *
     * @param StockTransfer $transfer
     * @return StockTransfer
     */
    private function loadTransferRelations(StockTransfer $transfer): StockTransfer
    {
        return $transfer->load([
            'fromWarehouse', 
            'toWarehouse', 
            'inventoryItem', 
            'transferredBy'
        ]);
    }
}
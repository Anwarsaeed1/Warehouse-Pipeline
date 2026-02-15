<?php

namespace App\Http\Controllers\API\Inventory;

use App\Filters\Inventory\InventoryItemNameFilter;
use App\Filters\Inventory\InventoryItemPriceRangeFilter;
use App\Filters\Inventory\ItemIsActiveFilter;
use App\Filters\Inventory\ItemNameFilter;
use App\Filters\Inventory\ItemPriceRangeFilter;
use App\Filters\Inventory\StockItemIsActiveFilter;
use App\Filters\Inventory\StockWarehouseIsActiveFilter;
use App\Filters\Inventory\WarehouseFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\InventoryPageRequest;
use App\Http\Requests\Inventory\UpdateStockRequest;
use App\Http\Resources\Inventory\InventoryItemResource;
use App\Http\Resources\Inventory\StockResource;
use App\Models\InventoryItem;
use App\Models\Stock;
use Illuminate\Http\JsonResponse;
use Illuminate\Pipeline\Pipeline;

class InventoryController extends Controller
{
    /**
     * GET /inventory - Paginated list of stock (inventory per warehouse) with filtering.
     */
    public function index(InventoryPageRequest $request): JsonResponse
    {
        $query = app(Pipeline::class)
            ->send(Stock::query()->with(['warehouse', 'inventoryItem']))
            ->through([
                WarehouseFilter::class,
                StockWarehouseIsActiveFilter::class,
                StockItemIsActiveFilter::class,
                InventoryItemNameFilter::class,
                InventoryItemPriceRangeFilter::class,
            ])
            ->thenReturn();

        $pageSize = $request->input('pageSize', 15);

        return successResponse(fetchData($query, $pageSize, StockResource::class));
    }

    /**
     * GET /inventory/items - Paginated list of inventory items (products) with filtering.
     */
    public function items(InventoryPageRequest $request): JsonResponse
    {
        $query = app(Pipeline::class)
            ->send(InventoryItem::query())
            ->through([
                ItemNameFilter::class,
                ItemPriceRangeFilter::class,
                ItemIsActiveFilter::class,
            ])
            ->thenReturn();

        $pageSize = $request->input('pageSize', 15);

        return successResponse(fetchData($query, $pageSize, InventoryItemResource::class));
    }

    /**
     * PUT /stocks/{stock} - Update stock quantity or reserved_quantity.
     */
    public function updateStock(UpdateStockRequest $request, Stock $stock): JsonResponse
    {
        $stock->update($request->only(['quantity', 'reserved_quantity']));
        return successResponse(new StockResource($stock->load(['warehouse', 'inventoryItem'])), __('api.updated_success'));
    }
}

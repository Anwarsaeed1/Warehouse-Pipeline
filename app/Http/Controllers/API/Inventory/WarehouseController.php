<?php

namespace App\Http\Controllers\API\Inventory;

use App\Filters\Inventory\InventoryItemNameFilter;
use App\Filters\Inventory\InventoryItemPriceRangeFilter;
use App\Filters\Inventory\StockItemIsActiveFilter;
use App\Filters\Inventory\WarehouseCodeFilter;
use App\Filters\Inventory\WarehouseIsActiveFilter;
use App\Filters\Inventory\WarehouseLocationFilter;
use App\Filters\Inventory\WarehouseNameFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\InventoryPageRequest;
use App\Http\Requests\Inventory\WarehouseListRequest;
use App\Http\Resources\Inventory\StockResource;
use App\Http\Resources\Inventory\WarehouseResource;
use App\Models\Stock;
use App\Models\Warehouse;
use Illuminate\Http\JsonResponse;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Cache;

class WarehouseController extends Controller
{
    /**
     * GET /warehouses - Paginated list of warehouses with filters (name, code, location).
     */
    public function index(WarehouseListRequest $request): JsonResponse
    {
        $query = app(Pipeline::class)
            ->send(Warehouse::query())
            ->through([
                WarehouseNameFilter::class,
                WarehouseCodeFilter::class,
                WarehouseLocationFilter::class,
                WarehouseIsActiveFilter::class,
            ])
            ->thenReturn();

        $pageSize = $request->input('pageSize', 15);

        return successResponse(fetchData($query, $pageSize, WarehouseResource::class));
    }

    /**
     * GET /warehouses/{warehouse}/inventory - Cached inventory for a specific warehouse.
     */
    public function inventory(InventoryPageRequest $request, Warehouse $warehouse): JsonResponse
    {
        $version = Cache::get("warehouse.inventory.{$warehouse->id}.version", 0);
        $cacheKey = "warehouse.inventory.{$warehouse->id}.v{$version}." . md5(json_encode($request->only(['search', 'price_min', 'price_max', 'page', 'pageSize', 'item_is_active'])));

        $data = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($request, $warehouse) {
            $query = app(Pipeline::class)
                ->send(
                    Stock::query()
                        ->where('warehouse_id', $warehouse->id)
                        ->with(['warehouse', 'inventoryItem'])
                )
                ->through([
                    StockItemIsActiveFilter::class,
                    InventoryItemNameFilter::class,
                    InventoryItemPriceRangeFilter::class,
                ])
                ->thenReturn();

            $pageSize = $request->input('pageSize', 15);

            return fetchData($query, $pageSize, StockResource::class);
        });

        return successResponse($data);
    }
}

<?php

use App\Http\Controllers\API\Auth\AuthController;
use App\Http\Controllers\API\Inventory\InventoryController;
use App\Http\Controllers\API\Inventory\StockTransferController;
use App\Http\Controllers\API\Inventory\WarehouseController;
use App\Models\InventoryItem;
use App\Models\Stock;
use App\Models\StockTransfer;
use App\Models\Warehouse;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/inventory', [InventoryController::class, 'index'])->can('viewAny', Stock::class);
    Route::get('/inventory/items', [InventoryController::class, 'items'])->can('viewAny', InventoryItem::class);
    Route::post('/stock-transfers', [StockTransferController::class, 'store'])->can('create', StockTransfer::class);
    Route::get('/warehouses', [WarehouseController::class, 'index'])->can('viewAny', Warehouse::class);
    Route::get('/warehouses/{warehouse}/inventory', [WarehouseController::class, 'inventory'])->can('view', 'warehouse');
    Route::put('/stocks/{stock}', [InventoryController::class, 'updateStock'])->can('update', 'stock');
});

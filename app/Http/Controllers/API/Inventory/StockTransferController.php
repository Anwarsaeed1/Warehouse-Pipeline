<?php

namespace App\Http\Controllers\API\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\StockTransfer\StockTransferRequest;
use App\Http\Resources\Inventory\StockTransferResource;
use App\Services\Inventory\StockTransferService;
use Illuminate\Http\JsonResponse;

class StockTransferController extends Controller
{
    public function __construct(
        protected StockTransferService $transferService
    ) {
    }

    public function store(StockTransferRequest $request): JsonResponse
    {
        $transfer = $this->transferService->transfer(
            $request->validated(),
            auth()->id()
        );

        return successResponse(new StockTransferResource($transfer), __('api.transfer_success'), 201);
    }
}

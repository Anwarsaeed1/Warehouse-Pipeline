<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Low stock threshold
    |--------------------------------------------------------------------------
    | When stock quantity falls at or below this value, LowStockDetected event is fired.
    */
    'low_stock_threshold' => (int) env('INVENTORY_LOW_STOCK_THRESHOLD', 5),
];

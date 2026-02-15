<?php

namespace App\Enum\Inventory;

use App\Trait\Global\EnumMethods;

enum StockTransferStatusEnum: string
{
    use EnumMethods;

    case Pending = 'pending';
    case Completed = 'completed';
    case Failed = 'failed';

    public static function keyName(): string
    {
        return 'stock_transfer_status';
    }
}

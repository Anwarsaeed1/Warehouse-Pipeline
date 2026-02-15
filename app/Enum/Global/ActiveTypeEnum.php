<?php

namespace App\Enum\Global;

use App\Trait\Global\EnumMethods;

enum ActiveTypeEnum: int
{
    use EnumMethods;

    case InActive = 0;
    case Active = 1;

    public static function keyName(): string
    {
        return 'active_type';
    }
}

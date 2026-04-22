<?php

namespace App\Enums;

enum ProductStatus: int
{
    case LISTED = 1;
    case DELISTED = 2;

    public function label(): string
    {
        return match ($this) {
            self::LISTED => '上架',
            self::DELISTED => '下架',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::LISTED => 'success',
            self::DELISTED => 'warning',
        };
    }
}

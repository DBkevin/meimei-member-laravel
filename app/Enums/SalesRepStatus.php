<?php
namespace App\Enums;
enum SalesRepStatus: int
{
    case ENABLED = 1;
    case DISABLED = 2;
    public function label(): string
    {
        return match ($this) {
            self::ENABLED => '启用',
            self::DISABLED => '禁用',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::ENABLED => 'success',
            self::DISABLED => 'danger',
        };
    }
}

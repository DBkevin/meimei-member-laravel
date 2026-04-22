<?php
namespace App\Enums;
enum ShowcaseStatus: int
{
    case VISIBLE = 1;
    case HIDDEN = 2;
    public function label(): string
    {
        return match ($this) {
            self::VISIBLE => '展示',
            self::HIDDEN => '隐藏',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::VISIBLE => 'success',
            self::HIDDEN => 'warning',
        };
    }
}

<?php

namespace App\Enums;

enum PointTransactionType: string
{
    case EARN = 'earn';
    case SPEND = 'spend';
    case ADJUST = 'adjust';
    case REFUND = 'refund';

    public function label(): string
    {
        return match ($this) {
            self::EARN => '获得',
            self::SPEND => '消费',
            self::ADJUST => '调整',
            self::REFUND => '退款',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::EARN => 'success',
            self::SPEND => 'danger',
            self::ADJUST => 'info',
            self::REFUND => 'warning',
        };
    }
}

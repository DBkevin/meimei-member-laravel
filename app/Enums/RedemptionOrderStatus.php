<?php

namespace App\Enums;

enum RedemptionOrderStatus: string
{
    case PENDING = 'pending';
    case VERIFIED = 'verified';
    case CANCELLED = 'cancelled';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => '待处理',
            self::VERIFIED => '已核销',
            self::CANCELLED => '已取消',
            self::REJECTED => '已拒绝',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::VERIFIED => 'success',
            self::CANCELLED => 'danger',
            self::REJECTED => 'gray',
        };
    }
}

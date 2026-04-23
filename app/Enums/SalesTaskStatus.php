<?php

namespace App\Enums;

enum SalesTaskStatus: string
{
    case PENDING = 'pending';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case OVERDUE = 'overdue';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => '待处理',
            self::IN_PROGRESS => '进行中',
            self::COMPLETED => '已完成',
            self::CANCELLED => '已取消',
            self::OVERDUE => '已逾期',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'gray',
            self::IN_PROGRESS => 'info',
            self::COMPLETED => 'success',
            self::CANCELLED => 'warning',
            self::OVERDUE => 'danger',
        };
    }

    public function canStart(): bool
    {
        return in_array($this, [self::PENDING, self::OVERDUE]);
    }

    public function canComplete(): bool
    {
        return in_array($this, [self::PENDING, self::IN_PROGRESS, self::OVERDUE]);
    }

    public function canCancel(): bool
    {
        return in_array($this, [self::PENDING, self::IN_PROGRESS, self::OVERDUE]);
    }
}
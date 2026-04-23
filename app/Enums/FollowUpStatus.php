<?php
namespace App\Enums;
enum FollowUpStatus: string
{
    case PENDING = 'pending';
    case COMPLETED = 'completed';
    case NEED_FOLLOW = 'need_follow';
    case DEAL = 'deal';
    case INVALID = 'invalid';
    public function label(): string
    { return match($this) {
        self::PENDING => '待跟进', self::COMPLETED => '已跟进', self::NEED_FOLLOW => '需再次跟进', self::DEAL => '已成交', self::INVALID => '无效',
    }; }
    public static function options(): array { return collect(self::cases())->mapWithKeys(fn($c) => [$c->value => $c->label()])->toArray(); }
}

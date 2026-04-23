<?php
namespace App\Enums;
enum FollowUpIntentionLevel: string
{
    case HIGH = 'high';
    case MEDIUM = 'medium';
    case LOW = 'low';
    case NONE = 'none';
    case DEAL = 'deal';
    public function label(): string
    {
        return match($this) {
            self::HIGH => '高意向',
            self::MEDIUM => '中意向',
            self::LOW => '低意向',
            self::NONE => '暂不考虑',
            self::DEAL => '已成交',
        };
    }
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn($case) => [$case->value => $case->label()])->toArray();
    }
}

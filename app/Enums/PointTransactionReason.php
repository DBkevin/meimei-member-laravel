<?php

namespace App\Enums;

/**
 * 积分原因/来源枚举
 */
enum PointTransactionReason: string
{
    // 获得积分
    case SHOP_REWARD = 'shop_reward';           // 到店奖励
    case CONSUME_REWARD = 'consume_reward';     // 消费奖励
    case RE_PURCHASE = 're_purchase';           // 老客复购
    case REFERRAL_REWARD = 'referral_reward';   // 转介绍奖励
    case ACTIVITY_REWARD = 'activity_reward';   // 活动奖励
    case MANUAL_ADJUST = 'manual_adjust';       // 手动调整
    case ORDER_REFUND = 'order_refund';         // 订单取消退还

    // 消耗积分
    case REDEMPTION = 'redemption';             // 兑换扣减

    // 系统
    case SYSTEM_ADJUST = 'system_adjust';       // 系统修正

    public function label(): string
    {
        return match ($this) {
            self::SHOP_REWARD => '到店奖励',
            self::CONSUME_REWARD => '消费奖励',
            self::RE_PURCHASE => '老客复购',
            self::REFERRAL_REWARD => '转介绍奖励',
            self::ACTIVITY_REWARD => '活动奖励',
            self::MANUAL_ADJUST => '手动调整',
            self::ORDER_REFUND => '订单取消退还',
            self::REDEMPTION => '兑换扣减',
            self::SYSTEM_ADJUST => '系统修正',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($case) => [$case->value => $case->label()])
            ->toArray();
    }
}
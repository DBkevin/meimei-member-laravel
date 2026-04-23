<?php

namespace App\Enums;

enum FollowUpType: string
{
    case FIRST_VISIT = 'first_visit';
    case AFTERCARE = 'aftercare';
    case REPURCHASE = 'repurchase';
    case BIRTHDAY_CARE = 'birthday_care';
    case CAMPAIGN_INVITE = 'campaign_invite';
    case REFERRAL = 'referral';
    case COMPLAINT = 'complaint';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::FIRST_VISIT => '首次回访',
            self::AFTERCARE => '术后回访',
            self::REPURCHASE => '复购提醒',
            self::BIRTHDAY_CARE => '生日关怀',
            self::CAMPAIGN_INVITE => '活动邀约',
            self::REFERRAL => '转介绍沟通',
            self::COMPLAINT => '投诉安抚',
            self::OTHER => '其他',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()])->toArray();
    }
}

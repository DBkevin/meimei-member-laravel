<?php

namespace App\Enums;

enum SalesTaskType: string
{
    case FOLLOW_UP = 'follow_up';
    case REVISIT = 'revisit';
    case SHOWCASE_COLLECT = 'showcase_collect';
    case REFERRAL_CONTACT = 'referral_contact';
    case ACTIVITY_INVITE = 'activity_invite';
    case AFTERCARE = 'aftercare';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::FOLLOW_UP => '客户跟进',
            self::REVISIT => '复购回访',
            self::SHOWCASE_COLLECT => '案例整理',
            self::REFERRAL_CONTACT => '转介绍沟通',
            self::ACTIVITY_INVITE => '活动邀约',
            self::AFTERCARE => '术后关怀',
            self::OTHER => '其他',
        };
    }
}
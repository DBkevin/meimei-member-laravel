<?php

namespace App\Enums;

enum FollowUpChannel: string
{
    case PHONE = 'phone';
    case WECHAT = 'wechat';
    case IN_STORE = 'in_store';
    case PLATFORM = 'platform';
    case SMS = 'sms';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::PHONE => '电话',
            self::WECHAT => '微信',
            self::IN_STORE => '到院面谈',
            self::PLATFORM => '平台沟通',
            self::SMS => '短信',
            self::OTHER => '其他',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()])->toArray();
    }
}

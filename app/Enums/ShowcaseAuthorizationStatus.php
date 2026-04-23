<?php
namespace App\Enums;
enum ShowcaseAuthorizationStatus: string
{
    case PENDING = 'pending';
    case AUTHORIZED = 'authorized';
    case REJECTED = 'rejected';
    case EXPIRED = 'expired';
    case WITHDRAWN = 'withdrawn';
    public function label(): string { return match($this) {
        self::PENDING => '待授权', self::AUTHORIZED => '已授权', self::REJECTED => '已拒绝', self::EXPIRED => '已过期', self::WITHDRAWN => '已撤回',
    }; }
    public static function options(): array { return collect(self::cases())->mapWithKeys(fn($c) => [$c->value => $c->label()])->toArray(); }
}

<?php
namespace App\Enums;
enum ShowcaseBeforeAfterType: string
{ case BEFORE = 'before'; case DURING = 'during'; case AFTER = 'after'; case COMPARISON = 'comparison';
    public function label(): string { return match($this) { self::BEFORE => '术前', self::DURING => '术中', self::AFTER => '术后', self::COMPARISON => '对比照', }; }
    public static function options(): array { return collect(self::cases())->mapWithKeys(fn($c) => [$c->value => $c->label()])->toArray(); } }

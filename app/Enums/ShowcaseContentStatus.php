<?php
namespace App\Enums;
enum ShowcaseContentStatus: string
{ case DRAFT = 'draft'; case EDITING = 'editing'; case READY = 'ready'; case ARCHIVED = 'archived';
    public function label(): string { return match($this) { self::DRAFT => '草稿', self::EDITING => '编辑中', self::READY => '待发布', self::ARCHIVED => '已归档', }; }
    public static function options(): array { return collect(self::cases())->mapWithKeys(fn($c) => [$c->value => $c->label()])->toArray(); }
}

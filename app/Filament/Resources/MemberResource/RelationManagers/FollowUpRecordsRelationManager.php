<?php
namespace App\Filament\Resources\MemberResource\RelationManagers;
use App\Models\FollowUpRecord;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables; use Filament\Tables\Table;
class FollowUpRecordsRelationManager extends RelationManager
{ protected static string $relationship = 'followUpRecords'; public function table(Table $table): Table { return $table->columns([
    Tables\Columns\TextColumn::make('type')->label('类型')->formatStateUsing(fn($state) => \App\Enums\FollowUpType::from($state)->label()),
    Tables\Columns\TextColumn::make('channel')->label('渠道')->formatStateUsing(fn($state) => \App\Enums\FollowUpChannel::from($state)->label()),
    Tables\Columns\TextColumn::make('content')->label('内容')->limit(30),
    Tables\Columns\TextColumn::make('intention_level')->label('意向')->formatStateUsing(fn($state) => $state ? \App\Enums\FollowUpIntentionLevel::from($state)->label() : '-'),
    Tables\Columns\TextColumn::make('status')->label('状态')->badge()->formatStateUsing(fn($state) => \App\Enums\FollowUpStatus::from($state)->label()),
    Tables\Columns\TextColumn::make('next_follow_up_at')->label('下次跟进')->dateTime('Y-m-d H:i'),
    Tables\Columns\TextColumn::make('created_at')->label('创建时间')->dateTime('Y-m-d H:i'),
])->defaultSort('created_at', 'desc')->actions([Tables\Actions\EditAction::make(), Tables\Actions\ViewAction::make()]); } }

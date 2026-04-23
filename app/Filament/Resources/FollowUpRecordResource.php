<?php
namespace App\Filament\Resources;
use App\Filament\Resources\FollowUpRecordResource\Pages;
use App\Models\FollowUpRecord;
use Filament\Forms; use Filament\Schemas\Schema;
use Filament\Resources\Resource; use Filament\Tables;
use Filament\Tables\Table;
class FollowUpRecordResource extends Resource
{
    protected static ?string $model = FollowUpRecord::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-phone';
    protected static ?string $navigationLabel = '跟进记录';
    protected static ?int $navigationSort = 6;
    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\Select::make('member_id')->label('会员')->relationship('member', 'name')->searchable()->required(),
            Forms\Components\Select::make('sales_rep_id')->label('销售')->relationship('salesRep', 'name')->searchable(),
            Forms\Components\Select::make('type')->label('跟进类型')->options(\App\Enums\FollowUpType::options())->required(),
            Forms\Components\Select::make('channel')->label('跟进渠道')->options(\App\Enums\FollowUpChannel::options())->required(),
            Forms\Components\Textarea::make('content')->label('跟进内容')->required(),
            Forms\Components\Select::make('intention_level')->label('意向等级')->options(\App\Enums\FollowUpIntentionLevel::options()),
            Forms\Components\DateTimePicker::make('next_follow_at')->label('下次跟进时间'),
            Forms\Components\Select::make('status')->label('状态')->options(\App\Enums\FollowUpStatus::options())->default('pending'),
            Forms\Components\Textarea::make('result')->label('跟进结果'),
        ]);
    }
    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),
            Tables\Columns\TextColumn::make('member.name')->label('会员')->searchable(),
            Tables\Columns\TextColumn::make('salesRep.name')->label('销售')->searchable(),
            Tables\Columns\TextColumn::make('type')->label('类型')->formatStateUsing(fn($state) => \App\Enums\FollowUpType::from($state)->label()),
            Tables\Columns\TextColumn::make('channel')->label('渠道')->formatStateUsing(fn($state) => \App\Enums\FollowUpChannel::from($state)->label()),
            Tables\Columns\TextColumn::make('intention_level')->label('意向')->formatStateUsing(fn($state) => $state ? \App\Enums\FollowUpIntentionLevel::from($state)->label() : '-'),
            Tables\Columns\TextColumn::make('status')->label('状态')->badge()->formatStateUsing(fn($state) => \App\Enums\FollowUpStatus::from($state)->label()),
            Tables\Columns\TextColumn::make('next_follow_at')->label('下次跟进')->dateTime('Y-m-d H:i'),
            Tables\Columns\TextColumn::make('created_at')->label('创建时间')->dateTime('Y-m-d H:i'),
        ])->filters([
            Tables\Filters\SelectFilter::make('sales_rep_id')->label('销售')->relationship('salesRep', 'name'),
            Tables\Filters\SelectFilter::make('status')->label('状态')->options(\App\Enums\FollowUpStatus::options()),
            Tables\Filters\SelectFilter::make('intention_level')->label('意向等级')->options(\App\Enums\FollowUpIntentionLevel::options()),
            Tables\Filters\SelectFilter::make('type')->label('跟进类型')->options(\App\Enums\FollowUpType::options()),
        ])->defaultSort('created_at', 'desc')->actions([Tables\Actions\EditAction::make(), Tables\Actions\ViewAction::make()]);
    }
    public static function getPages(): array { return ['index' => Pages\ListFollowUpRecords::route('/'), 'create' => Pages\CreateFollowUpRecord::route('/create'), 'edit' => Pages\EditFollowUpRecord::route('/{record}/edit'), 'view' => Pages\ViewFollowUpRecord::route('/{record}')]; }
}

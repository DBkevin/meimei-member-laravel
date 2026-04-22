<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PointTransactionResource\Pages;
use App\Models\PointTransaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\BadgeColumn;
use App\Enums\PointTransactionType;

class PointTransactionResource extends Resource
{
    protected static ?string $model = PointTransaction::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-arrow-path';

    protected static ?string $navigationLabel = '积分流水';

    protected static ?string $modelLabel = '积分流水';

    protected static ?string $pluralModelLabel = '积分流水';

    public static function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->schema([
                Forms\Components\Section::make('流水信息')
                    ->schema([
                        Forms\Components\Select::make('member_id')
                            ->label('会员')
                            ->relationship('member', 'name')
                            ->required()
                            ->disabled(),
                        Forms\Components\Select::make('point_account_id')
                            ->label('积分账户')
                            ->relationship('pointAccount', 'id')
                            ->required()
                            ->disabled(),
                        Forms\Components\Select::make('type')
                            ->label('类型')
                            ->options(PointTransactionType::class)
                            ->required()
                            ->disabled(),
                        Forms\Components\TextInput::make('points')
                            ->label('积分')
                            ->numeric()
                            ->required()
                            ->disabled(),
                        Forms\Components\TextInput::make('before_balance')
                            ->label('变更前余额')
                            ->numeric()
                            ->disabled(),
                        Forms\Components\TextInput::make('after_balance')
                            ->label('变更后余额')
                            ->numeric()
                            ->disabled(),
                        Forms\Components\TextInput::make('operator_name')
                            ->label('操作人')
                            ->disabled(),
                        Forms\Components\Textarea::make('remark')
                            ->label('备注')
                            ->disabled(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('member.name')
                    ->label('会员')
                    ->searchable(),
                Tables\Columns\TextColumn::make('member.phone')
                    ->label('手机号')
                    ->searchable(),
                BadgeColumn::make('type')
                    ->label('类型')
                    ->formatStateUsing(fn (PointTransactionType $state) => $state->label())
                    ->colors([
                        'success' => PointTransactionType::EARN,
                        'danger' => PointTransactionType::SPEND,
                        'info' => PointTransactionType::ADJUST,
                        'warning' => PointTransactionType::REFUND,
                    ]),
                Tables\Columns\TextColumn::make('points')
                    ->label('积分')
                    ->numeric(),
                Tables\Columns\TextColumn::make('before_balance')
                    ->label('前余')
                    ->numeric(),
                Tables\Columns\TextColumn::make('after_balance')
                    ->label('后余')
                    ->numeric(),
                Tables\Columns\TextColumn::make('operator_name')
                    ->label('操作人'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('时间')
                    ->dateTime('Y-m-d H:i:s'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('类型')
                    ->options(PointTransactionType::class),
                Tables\Filters\SelectFilter::make('member_id')
                    ->label('会员')
                    ->relationship('member', 'name'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPointTransactions::route('/'),
            'view' => Pages\ViewPointTransaction::route('/{record}'),
        ];
    }
}

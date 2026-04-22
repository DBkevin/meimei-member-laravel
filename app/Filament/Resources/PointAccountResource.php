<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PointAccountResource\Pages;
use App\Models\PointAccount;
use Filament\Forms;
use Filament\Forms\Form;
// Resource import kept
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PointAccountResource extends Resource
{
    protected static ?string $model = PointAccount::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-wallet';

    protected static ?string $navigationLabel = '积分账户';

    protected static ?string $modelLabel = '积分账户';

    protected static ?string $pluralModelLabel = '积分账户';

    public static function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->schema([
                Forms\Components\Section::make('账户信息')
                    ->schema([
                        Forms\Components\Select::make('member_id')
                            ->label('会员')
                            ->relationship('member', 'name')
                            ->required()
                            ->disabled(fn (string $operation) => $operation === 'edit'),
                        Forms\Components\TextInput::make('balance')
                            ->label('余额')
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('total_earned')
                            ->label('总获得')
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('total_spent')
                            ->label('总消费')
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('frozen_points')
                            ->label('冻结积分')
                            ->numeric()
                            ->default(0),
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
                    ->label('手机号'),
                Tables\Columns\TextColumn::make('balance')
                    ->label('余额')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_earned')
                    ->label('总获得')
                    ->numeric(),
                Tables\Columns\TextColumn::make('total_spent')
                    ->label('总消费')
                    ->numeric(),
                Tables\Columns\TextColumn::make('frozen_points')
                    ->label('冻结积分')
                    ->numeric(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListPointAccounts::route('/'),
            'create' => Pages\CreatePointAccount::route('/create'),
            'edit' => Pages\EditPointAccount::route('/{record}/edit'),
            'view' => Pages\ViewPointAccount::route('/{record}'),
        ];
    }
}

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PointProductResource\Pages;
use App\Models\PointProduct;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\BadgeColumn;
use App\Enums\ProductStatus;

class PointProductResource extends Resource
{
    protected static ?string $model = PointProduct::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-gift';

    protected static ?string $navigationLabel = '商品管理';

    protected static ?string $modelLabel = '商品';

    protected static ?string $pluralModelLabel = '商品';

    public static function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->schema([
                Forms\Components\Section::make('商品信息')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('商品名称')
                            ->required(),
                        Forms\Components\TextInput::make('cover_url')
                            ->label('封面图URL')
                            ->nullable(),
                        Forms\Components\TextInput::make('category')
                            ->label('分类')
                            ->nullable(),
                        Forms\Components\TextInput::make('points_price')
                            ->label('积分价格')
                            ->numeric()
                            ->required(),
                        Forms\Components\TextInput::make('stock')
                            ->label('库存')
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('sort')
                            ->label('排序')
                            ->numeric()
                            ->default(0),
                        Forms\Components\Textarea::make('description')
                            ->label('描述')
                            ->nullable(),
                        Forms\Components\Select::make('status')
                            ->label('状态')
                            ->options(ProductStatus::class)
                            ->default(ProductStatus::LISTED),
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
                Tables\Columns\TextColumn::make('name')
                    ->label('商品名称')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category')
                    ->label('分类'),
                Tables\Columns\TextColumn::make('points_price')
                    ->label('积分价格')
                    ->numeric(),
                Tables\Columns\TextColumn::make('stock')
                    ->label('库存')
                    ->numeric(),
                BadgeColumn::make('status')
                    ->label('状态')
                    ->formatStateUsing(fn (ProductStatus $state) => $state->label())
                    ->colors(['success' => ProductStatus::LISTED, 'warning' => ProductStatus::DELISTED]),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('创建时间')
                    ->dateTime('Y-m-d H:i:s'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('状态')
                    ->options(ProductStatus::class),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListPointProducts::route('/'),
            'create' => Pages\CreatePointProduct::route('/create'),
            'edit' => Pages\EditPointProduct::route('/{record}/edit'),
            'view' => Pages\ViewPointProduct::route('/{record}'),
        ];
    }
}

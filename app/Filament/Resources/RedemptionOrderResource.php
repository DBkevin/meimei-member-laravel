<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RedemptionOrderResource\Pages;
use App\Models\RedemptionOrder;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\BadgeColumn;
use App\Enums\RedemptionOrderStatus;
use Illuminate\Support\Str;

class RedemptionOrderResource extends Resource
{
    protected static ?string $model = RedemptionOrder::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationLabel = '兑换订单';

    protected static ?string $modelLabel = '兑换订单';

    protected static ?string $pluralModelLabel = '兑换订单';

    public static function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->schema([
                Forms\Components\Section::make('订单信息')
                    ->schema([
                        Forms\Components\TextInput::make('order_no')
                            ->label('订单号')
                            ->disabled(),
                        Forms\Components\Select::make('member_id')
                            ->label('会员')
                            ->relationship('member', 'name')
                            ->required(),
                        Forms\Components\Select::make('point_product_id')
                            ->label('商品')
                            ->relationship('pointProduct', 'name')
                            ->required(),
                        Forms\Components\TextInput::make('product_name')
                            ->label('商品名称')
                            ->required(),
                        Forms\Components\TextInput::make('quantity')
                            ->label('数量')
                            ->numeric()
                            ->default(1),
                        Forms\Components\TextInput::make('unit_points')
                            ->label('单价积分')
                            ->numeric()
                            ->required(),
                        Forms\Components\TextInput::make('total_points')
                            ->label('总积分')
                            ->numeric()
                            ->required(),
                        Forms\Components\TextInput::make('receiver_name')
                            ->label('收货人姓名')
                            ->nullable(),
                        Forms\Components\TextInput::make('receiver_phone')
                            ->label('收货人电话')
                            ->nullable(),
                        Forms\Components\Select::make('verify_sales_rep_id')
                            ->label('核销销售')
                            ->relationship('verifier', 'name')
                            ->nullable(),
                        Forms\Components\Select::make('status')
                            ->label('状态')
                            ->options(RedemptionOrderStatus::class)
                            ->default(RedemptionOrderStatus::PENDING),
                        Forms\Components\Textarea::make('remark')
                            ->label('备注')
                            ->nullable(),
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
                Tables\Columns\TextColumn::make('order_no')
                    ->label('订单号')
                    ->searchable(),
                Tables\Columns\TextColumn::make('member.name')
                    ->label('会员')
                    ->searchable(),
                Tables\Columns\TextColumn::make('member.phone')
                    ->label('手机号')
                    ->searchable(),
                Tables\Columns\TextColumn::make('product_name')
                    ->label('商品'),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('数量'),
                Tables\Columns\TextColumn::make('total_points')
                    ->label('积分')
                    ->numeric(),
                BadgeColumn::make('status')
                    ->label('状态')
                    ->formatStateUsing(fn (RedemptionOrderStatus $state) => $state->label())
                    ->colors([
                        'warning' => RedemptionOrderStatus::PENDING,
                        'success' => RedemptionOrderStatus::COMPLETED,
                        'danger' => RedemptionOrderStatus::CANCELLED,
                    ]),
                Tables\Columns\TextColumn::make('verifier.name')
                    ->label('核销人'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('创建时间')
                    ->dateTime('Y-m-d H:i:s'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('状态')
                    ->options(RedemptionOrderStatus::class),
                Tables\Filters\SelectFilter::make('member_id')
                    ->label('会员')
                    ->relationship('member', 'name'),
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
            'index' => Pages\ListRedemptionOrders::route('/'),
            'create' => Pages\CreateRedemptionOrder::route('/create'),
            'edit' => Pages\EditRedemptionOrder::route('/{record}/edit'),
            'view' => Pages\ViewRedemptionOrder::route('/{record}'),
        ];
    }
}

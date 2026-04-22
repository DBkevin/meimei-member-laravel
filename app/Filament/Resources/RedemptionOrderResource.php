<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RedemptionOrderResource\Pages;
use App\Models\RedemptionOrder;
use App\Services\PointRedemptionService;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\BadgeColumn;
use App\Enums\RedemptionOrderStatus;

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
                        'success' => RedemptionOrderStatus::VERIFIED,
                        'danger' => RedemptionOrderStatus::CANCELLED,
                        'gray' => RedemptionOrderStatus::REJECTED,
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
                Tables\Actions\ViewAction::make(),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('verify')
                        ->label('核销')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn (RedemptionOrder $record) => $record->status === RedemptionOrderStatus::PENDING)
                        ->form([
                            Forms\Components\Select::make('sales_rep_id')
                                ->label('核销人')
                                ->relationship('verifier', 'name')
                                ->nullable(),
                            Forms\Components\Textarea::make('remark')
                                ->label('备注'),
                        ])
                        ->action(function (RedemptionOrder $record, array $data) {
                            $service = app(PointRedemptionService::class);
                            $service->verify($record, $data['sales_rep_id'] ?? null, $data['remark'] ?? null);
                        }),
                    Tables\Actions\Action::make('cancel')
                        ->label('取消')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->visible(fn (RedemptionOrder $record) => $record->status === RedemptionOrderStatus::PENDING)
                        ->form([
                            Forms\Components\Textarea::make('remark')
                                ->label('取消原因'),
                        ])
                        ->action(function (RedemptionOrder $record, array $data) {
                            $service = app(PointRedemptionService::class);
                            $service->cancel($record, null, $data['remark'] ?? null);
                        }),
                    Tables\Actions\Action::make('reject')
                        ->label('拒绝')
                        ->icon('heroicon-o-no-symbol')
                        ->color('gray')
                        ->visible(fn (RedemptionOrder $record) => $record->status === RedemptionOrderStatus::PENDING)
                        ->form([
                            Forms\Components\Textarea::make('remark')
                                ->label('拒绝原因'),
                        ])
                        ->action(function (RedemptionOrder $record, array $data) {
                            $service = app(PointRedemptionService::class);
                            $service->reject($record, null, $data['remark'] ?? null);
                        }),
                ]),
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
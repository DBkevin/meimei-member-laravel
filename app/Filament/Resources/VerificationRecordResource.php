<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VerificationRecordResource\Pages;
use App\Models\VerificationRecord;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class VerificationRecordResource extends Resource
{
    protected static ?string $model = VerificationRecord::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-check-circle';

    protected static ?string $navigationLabel = '核销记录';

    protected static ?string $modelLabel = '核销记录';

    protected static ?string $pluralModelLabel = '核销记录';

    public static function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->schema([
                Forms\Components\Section::make('核销信息')
                    ->schema([
                        Forms\Components\Select::make('redemption_order_id')
                            ->label('兑换订单')
                            ->relationship('redemptionOrder', 'order_no')
                            ->required(),
                        Forms\Components\Select::make('member_id')
                            ->label('会员')
                            ->relationship('member', 'name')
                            ->required(),
                        Forms\Components\Select::make('sales_rep_id')
                            ->label('核销销售')
                            ->relationship('salesRep', 'name')
                            ->nullable(),
                        Forms\Components\DateTimePicker::make('verified_at')
                            ->label('核销时间')
                            ->required(),
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
                Tables\Columns\TextColumn::make('redemptionOrder.order_no')
                    ->label('订单号')
                    ->searchable(),
                Tables\Columns\TextColumn::make('member.name')
                    ->label('会员')
                    ->searchable(),
                Tables\Columns\TextColumn::make('member.phone')
                    ->label('手机号')
                    ->searchable(),
                Tables\Columns\TextColumn::make('salesRep.name')
                    ->label('核销销售'),
                Tables\Columns\TextColumn::make('verified_at')
                    ->label('核销时间')
                    ->dateTime('Y-m-d H:i:s'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('创建时间')
                    ->dateTime('Y-m-d H:i:s'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('member_id')
                    ->label('会员')
                    ->relationship('member', 'name'),
                Tables\Filters\SelectFilter::make('sales_rep_id')
                    ->label('销售')
                    ->relationship('salesRep', 'name'),
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
            'index' => Pages\ListVerificationRecords::route('/'),
            'create' => Pages\CreateVerificationRecord::route('/create'),
            'edit' => Pages\EditVerificationRecord::route('/{record}/edit'),
            'view' => Pages\ViewVerificationRecord::route('/{record}'),
        ];
    }
}

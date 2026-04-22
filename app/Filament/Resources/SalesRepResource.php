<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SalesRepResource\Pages;
use App\Models\SalesRep;
use Filament\Forms;
// Form import removed - using Schema in Filament 5.5
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\BadgeColumn;
use App\Enums\SalesRepStatus;

class SalesRepResource extends Resource
{
    protected static ?string $model = SalesRep::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = '销售管理';

    protected static ?string $modelLabel = '销售';

    protected static ?string $pluralModelLabel = '销售';

    public static function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->schema([
                Forms\Components\Section::make('基本信息')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('姓名')
                            ->required(),
                        Forms\Components\TextInput::make('phone')
                            ->label('手机号')
                            ->nullable(),
                        Forms\Components\Select::make('user_id')
                            ->label('关联账号')
                            ->relationship('user', 'name')
                            ->nullable(),
                        Forms\Components\Select::make('status')
                            ->label('状态')
                            ->options(SalesRepStatus::class)
                            ->default(SalesRepStatus::ENABLED),
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
                Tables\Columns\TextColumn::make('name')
                    ->label('姓名')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('手机号')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('关联账号'),
                BadgeColumn::make('status')
                    ->label('状态')
                    ->formatStateUsing(fn (SalesRepStatus $state) => $state->label())
                    ->colors(['success' => SalesRepStatus::ENABLED, 'danger' => SalesRepStatus::DISABLED]),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('创建时间')
                    ->dateTime('Y-m-d H:i:s'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('状态')
                    ->options(SalesRepStatus::class),
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
            'index' => Pages\ListSalesReps::route('/'),
            'create' => Pages\CreateSalesRep::route('/create'),
            'edit' => Pages\EditSalesRep::route('/{record}/edit'),
            'view' => Pages\ViewSalesRep::route('/{record}'),
        ];
    }
}

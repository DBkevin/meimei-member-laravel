<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DoctorResource\Pages;
use App\Models\Doctor;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\BadgeColumn;
use App\Enums\DoctorStatus;

class DoctorResource extends Resource
{
    protected static ?string $model = Doctor::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationLabel = '医生管理';

    protected static ?string $modelLabel = '医生';

    protected static ?string $pluralModelLabel = '医生';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Section::make('基本信息')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('医生姓名')
                            ->required(),
                        Forms\Components\TextInput::make('title')
                            ->label('职称')
                            ->nullable(),
                        Forms\Components\TextInput::make('avatar')
                            ->label('头像URL')
                            ->nullable(),
                        Forms\Components\Textarea::make('intro')
                            ->label('介绍')
                            ->nullable(),
                        Forms\Components\TextInput::make('sort')
                            ->label('排序')
                            ->numeric()
                            ->default(0),
                        Forms\Components\Select::make('status')
                            ->label('状态')
                            ->options(DoctorStatus::class)
                            ->default(DoctorStatus::ENABLED),
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
                Tables\Columns\TextColumn::make('title')
                    ->label('职称'),
                Tables\Columns\TextColumn::make('showcases_count')
                    ->label('案例数')
                    ->counts('showcases'),
                BadgeColumn::make('status')
                    ->label('状态')
                    ->formatStateUsing(fn (DoctorStatus $state) => $state->label())
                    ->colors(['success' => DoctorStatus::ENABLED, 'danger' => DoctorStatus::DISABLED]),
                Tables\Columns\TextColumn::make('sort')
                    ->label('排序'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('创建时间')
                    ->dateTime('Y-m-d H:i:s'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('状态')
                    ->options(DoctorStatus::class),
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
            'index' => Pages\ListDoctors::route('/'),
            'create' => Pages\CreateDoctor::route('/create'),
            'edit' => Pages\EditDoctor::route('/{record}/edit'),
            'view' => Pages\ViewDoctor::route('/{record}'),
        ];
    }
}

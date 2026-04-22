<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShowcaseResource\Pages;
use App\Models\Showcase;
use Filament\Forms;
// Form import removed - using Schema in Filament 5.5
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\BadgeColumn;
use App\Enums\ShowcaseStatus;
use App\Enums\MediaType;

class ShowcaseResource extends Resource
{
    protected static ?string $model = Showcase::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationLabel = '案例管理';

    protected static ?string $modelLabel = '案例';

    protected static ?string $pluralModelLabel = '案例';

    protected static ?string $navigationParentItem = '医生管理';

    public static function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->schema([
                Forms\Components\Section::make('案例信息')
                    ->schema([
                        Forms\Components\Select::make('doctor_id')
                            ->label('医生')
                            ->relationship('doctor', 'name')
                            ->required(),
                        Forms\Components\TextInput::make('title')
                            ->label('案例标题')
                            ->required(),
                        Forms\Components\TextInput::make('cover_url')
                            ->label('封面图URL')
                            ->nullable(),
                        Forms\Components\Select::make('media_type')
                            ->label('媒体类型')
                            ->options(MediaType::class)
                            ->default(MediaType::IMAGE),
                        Forms\Components\TextInput::make('media_url')
                            ->label('媒体URL')
                            ->nullable(),
                        Forms\Components\Textarea::make('content')
                            ->label('案例内容')
                            ->nullable(),
                        Forms\Components\TextInput::make('project_name')
                            ->label('项目名称')
                            ->nullable(),
                        Forms\Components\TextInput::make('sort')
                            ->label('排序')
                            ->numeric()
                            ->default(0),
                        Forms\Components\Select::make('status')
                            ->label('状态')
                            ->options(ShowcaseStatus::class)
                            ->default(ShowcaseStatus::VISIBLE),
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
                Tables\Columns\TextColumn::make('doctor.name')
                    ->label('医生')
                    ->searchable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('标题')
                    ->searchable(),
                Tables\Columns\TextColumn::make('project_name')
                    ->label('项目')
                    ->searchable(),
                Tables\Columns\TextColumn::make('media_type')
                    ->label('媒体类型'),
                BadgeColumn::make('status')
                    ->label('状态')
                    ->formatStateUsing(fn (ShowcaseStatus $state) => $state->label())
                    ->colors(['success' => ShowcaseStatus::VISIBLE, 'warning' => ShowcaseStatus::HIDDEN]),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('创建时间')
                    ->dateTime('Y-m-d H:i:s'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('状态')
                    ->options(ShowcaseStatus::class),
                Tables\Filters\SelectFilter::make('doctor_id')
                    ->label('医生')
                    ->relationship('doctor', 'name'),
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
            'index' => Pages\ListShowcases::route('/'),
            'create' => Pages\CreateShowcase::route('/create'),
            'edit' => Pages\EditShowcase::route('/{record}/edit'),
            'view' => Pages\ViewShowcase::route('/{record}'),
        ];
    }
}

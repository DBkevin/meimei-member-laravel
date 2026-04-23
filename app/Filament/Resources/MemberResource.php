<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MemberResource\Pages;
use App\Models\Member;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

use Filament\Tables\Columns\BadgeColumn;
use App\Enums\MemberStatus;

class MemberResource extends Resource
{
    protected static ?string $model = Member::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = '会员管理';

    protected static ?string $modelLabel = '会员';

    protected static ?string $pluralModelLabel = '会员';

    public static function form(Schema $schema): Schema
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
                            ->required()
                            ->unique(ignoreRecord: true),
                        Forms\Components\Select::make('gender')
                            ->label('性别')
                            ->options([
                                'male' => '男',
                                'female' => '女',
                            ])
                            ->nullable(),
                        Forms\Components\DatePicker::make('birthday')
                            ->label('生日')
                            ->nullable(),
                        Forms\Components\TextInput::make('crm_archive_no')
                            ->label('档案号')
                            ->nullable(),
                        Forms\Components\TextInput::make('source')
                            ->label('来源渠道')
                            ->nullable(),
                        Forms\Components\TextInput::make('level')
                            ->label('会员等级')
                            ->nullable(),
                        Forms\Components\Select::make('sales_rep_id')
                            ->label('归属销售')
                            ->relationship('salesRep', 'name')
                            ->nullable(),
                        Forms\Components\Select::make('status')
                            ->label('状态')
                            ->options(MemberStatus::class)
                            ->default(MemberStatus::ENABLED),
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
                Tables\Columns\TextColumn::make('crm_archive_no')
                    ->label('档案号')
                    ->searchable(),
                Tables\Columns\TextColumn::make('salesRep.name')
                    ->label('归属销售'),
                BadgeColumn::make('status')
                    ->label('状态')
                    ->formatStateUsing(fn (MemberStatus $state) => $state->label())
                    ->colors(['success' => MemberStatus::ENABLED, 'danger' => MemberStatus::DISABLED]),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('创建时间')
                    ->dateTime('Y-m-d H:i:s'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('状态')
                    ->options(MemberStatus::class),
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
            \Filament\Resources\RelationManagers\RelationManager::make([
                "label" => "跟进记录",
                "manager" => \App\Filament\Resources\MemberResource\RelationManagers\FollowUpRecordsRelationManager::class,
            ]),
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMembers::route('/'),
            'create' => Pages\CreateMember::route('/create'),
            'edit' => Pages\EditMember::route('/{record}/edit'),
            'view' => Pages\ViewMember::route('/{record}'),
        ];
    }
}

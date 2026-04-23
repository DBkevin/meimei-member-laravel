<?php
namespace App\Filament\Resources; use App\Filament\Resources\ShowcaseResource\Pages; use App\Models\Showcase; use Filament\Forms; use Filament\Schemas\Schema; use Filament\Resources\Resource; use Filament\Tables; use Filament\Tables\Table; use Filament\Tables\Columns\BadgeColumn; use App\Enums\ShowcaseStatus; use App\Enums\MediaType; use App\Enums\ShowcaseAuthorizationStatus; use App\Enums\ShowcaseContentStatus; use App\Enums\ShowcaseBeforeAfterType;
class ShowcaseResource extends Resource { protected static ?string $model = Showcase::class; protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-photo'; protected static ?string $navigationLabel = '案例管理'; protected static ?string $modelLabel = '案例'; protected static ?string $pluralModelLabel = '案例'; protected static ?string $navigationParentItem = '医生管理'; public static function form(Schema $schema): Schema { return $schema->schema([
    Forms\Components\Section::make('基本信息')->schema([
        Forms\Components\Select::make('doctor_id')->label('医生')->relationship('doctor', 'name')->required(),
        Forms\Components\Select::make('member_id')->label('会员')->relationship('member', 'name')->searchable()->nullable(),
        Forms\Components\Select::make('sales_rep_id')->label('销售')->relationship('salesRep', 'name')->searchable()->nullable(),
        Forms\Components\TextInput::make('title')->label('案例标题')->required(),
        Forms\Components\TextInput::make('cover_url')->label('封面图URL')->nullable(),
    ])->columns(2),
    Forms\Components\Section::make('项目与素材')->schema([
        Forms\Components\TextInput::make('project_name')->label('项目名称')->nullable(),
        Forms\Components\TextInput::make('project_type')->label('项目类型')->nullable(),
        Forms\Components\Select::make('media_type')->label('媒体类型')->options(MediaType::class)->default(MediaType::IMAGE),
        Forms\Components\TextInput::make('media_url')->label('媒体URL')->nullable(),
        Forms\Components\Select::make('before_after_type')->label('术前术后类型')->options(ShowcaseBeforeAfterType::class)->nullable(),
    ])->columns(2),
    Forms\Components\Section::make('内容与状态')->schema([
        Forms\Components\Textarea::make('content')->label('案例内容')->nullable(),
        Forms\Components\Select::make('status')->label('状态')->options(ShowcaseStatus::class)->default(ShowcaseStatus::VISIBLE),
        Forms\Components\Select::make('authorization_status')->label('授权状态')->options(ShowcaseAuthorizationStatus::class)->default('pending'),
        Forms\Components\Select::make('content_status')->label('素材状态')->options(ShowcaseContentStatus::class)->default('draft'),
        Forms\Components\TextInput::make('sort')->label('排序')->numeric()->default(0),
    ])->columns(2),
    Forms\Components\Section::make('渠道用途')->schema([
        Forms\Components\Toggle::make('is_featured')->label('重点案例'),
        Forms\Components\Toggle::make('is_public')->label('公开'),
        Forms\Components\Toggle::make('usable_for_wechat')->label('朋友圈可用'),
        Forms\Components\Toggle::make('usable_for_article')->label('公众号可用'),
        Forms\Components\Toggle::make('usable_for_xiaohongshu')->label('小红书可用'),
    ])->columns(2),
    Forms\Components\Section::make('其他')->schema([
        Forms\Components\TagsInput::make('tags')->label('标签')->nullable(),
        Forms\Components\Textarea::make('remark')->label('备注')->nullable(),
    ]),
]); }
    public static function table(Table $table): Table { return $table->columns([
    Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),
    Tables\Columns\TextColumn::make('doctor.name')->label('医生')->searchable(),
    Tables\Columns\TextColumn::make('title')->label('标题')->searchable(),
    Tables\Columns\TextColumn::make('project_name')->label('项目')->searchable(),
    Tables\Columns\TextColumn::make('project_type')->label('类型'),
    BadgeColumn::make('authorization_status')->label('授权')->formatStateUsing(fn($s) => \App\Enums\ShowcaseAuthorizationStatus::from($s)->label())->colors(['warning' => 'pending', 'success' => 'authorized', 'danger' => 'rejected']),
    BadgeColumn::make('content_status')->label('素材')->formatStateUsing(fn($s) => \App\Enums\ShowcaseContentStatus::from($s)->label())->colors(['gray' => 'draft', 'info' => 'editing', 'success' => 'ready']),
    Tables\Columns\IconColumn::make('is_featured')->label('重点')->boolean(),
    Tables\Columns\IconColumn::make('usable_for_wechat')->label('朋友圈')->boolean(),
    Tables\Columns\IconColumn::make('usable_for_article')->label('公众号')->boolean(),
    Tables\Columns\IconColumn::make('usable_for_xiaohongshu')->label('小红书')->boolean(),
    BadgeColumn::make('status')->label('状态')->formatStateUsing(fn(ShowcaseStatus $s) => $s->label())->colors(['success' => ShowcaseStatus::VISIBLE, 'warning' => ShowcaseStatus::HIDDEN]),
    Tables\Columns\TextColumn::make('created_at')->label('创建时间')->dateTime('Y-m-d'),
])->defaultSort('created_at', 'desc')->filters([
    Tables\Filters\SelectFilter::make('project_type')->label('项目类型')->options(['祛痘' => '祛痘', '痘坑' => '痘坑', '疤痕' => '疤痕', '光子嫩肤' => '光子嫩肤', '黄金微针' => '黄金微针', '眼整形' => '眼整形']),
    Tables\Filters\SelectFilter::make('doctor_id')->label('医生')->relationship('doctor', 'name'),
    Tables\Filters\SelectFilter::make('authorization_status')->label('授权状态')->options(ShowcaseAuthorizationStatus::options()),
    Tables\Filters\SelectFilter::make('content_status')->label('素材状态')->options(ShowcaseContentStatus::options()),
    Tables\Filters\Filter::make('usable_for_wechat')->label('朋友圈可用')->query(fn($q) => $q->where('usable_for_wechat', true)),
    Tables\Filters\Filter::make('usable_for_article')->label('公众号可用')->query(fn($q) => $q->where('usable_for_article', true)),
    Tables\Filters\Filter::make('usable_for_xiaohongshu')->label('小红书可用')->query(fn($q) => $q->where('usable_for_xiaohongshu', true)),
    Tables\Filters\Filter::make('is_featured')->label('重点案例')->query(fn($q) => $q->where('is_featured', true)),
])->actions([Tables\Actions\EditAction::make(), Tables\Actions\ViewAction::make()]); }
    public static function getRelations(): array { return []; }
    public static function getPages(): array { return ['index' => Pages\ListShowcases::route('/'), 'create' => Pages\CreateShowcase::route('/create'), 'edit' => Pages\EditShowcase::route('/{record}/edit'), 'view' => Pages\ViewShowcase::route('/{record}')]; }
}

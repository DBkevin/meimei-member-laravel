<?php
namespace App\Filament\Resources\DoctorResource\RelationManagers; use App\Models\Showcase; use Filament\Resources\RelationManagers\RelationManager; use Filament\Tables; use Filament\Tables\Table; use Filament\Tables\Columns\BadgeColumn; use Filament\Tables\Columns\IconColumn;
class ShowcasesRelationManager extends RelationManager { protected static ?string $title = '案例素材'; protected static ?string $modelLabel = '案例'; protected static string $relationship = 'showcases'; public function table(Table $table): Table { return $table->columns([
    Tables\Columns\TextColumn::make('title')->label('标题')->limit(30),
    Tables\Columns\TextColumn::make('project_name')->label('项目'),
    Tables\Columns\TextColumn::make('member.name')->label('会员'),
    BadgeColumn::make('authorization_status')->label('授权')->formatStateUsing(fn($s) => \App\Enums\ShowcaseAuthorizationStatus::from($s)->label())->colors(['warning' => 'pending', 'success' => 'authorized']),
    IconColumn::make('is_featured')->label('重点')->boolean(),
    IconColumn::make('usable_for_wechat')->label('朋友圈')->boolean(),
])->paginated(10); }
}

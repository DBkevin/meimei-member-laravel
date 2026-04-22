<?php

namespace App\Filament\Resources\MemberResource\Pages;

use App\Filament\Resources\MemberResource;
use App\Models\Member;
use App\Services\PointAdjustmentService;
use App\Enums\PointTransactionReason;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Pages\ViewRecord;

class ViewMember extends ViewRecord
{
    protected static string $resource = MemberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            Action::make('发放积分')
                ->label('发放积分')
                ->icon('heroicon-o-plus-circle')
                ->form([
                    TextInput::make('points')
                        ->label('积分数量')
                        ->required()
                        ->numeric()
                        ->minValue(1),
                    Select::make('reason')
                        ->label('原因')
                        ->required()
                        ->options(PointTransactionReason::options()),
                    Textarea::make('remark')
                        ->label('备注')
                        ->nullable(),
                ])
                ->action(function (Member $record, array $data) {
                    $service = app(PointAdjustmentService::class);
                    $service->earn(
                        $record,
                        $data['points'],
                        $data['reason'],
                        $data['remark'] ?? null,
                        auth()->id()
                    );
                    \Filament\Notifications\Notification::make()
                        ->title('积分发放成功')
                        ->success()
                        ->send();
                }),
            Action::make('扣减积分')
                ->label('扣减积分')
                ->icon('heroicon-o-minus-circle')
                ->color('danger')
                ->form([
                    TextInput::make('points')
                        ->label('积分数量')
                        ->required()
                        ->numeric()
                        ->minValue(1),
                    Select::make('reason')
                        ->label('原因')
                        ->required()
                        ->options(PointTransactionReason::options()),
                    Textarea::make('remark')
                        ->label('备注')
                        ->nullable(),
                ])
                ->action(function (Member $record, array $data) {
                    $service = app(PointAdjustmentService::class);
                    try {
                        $service->spend(
                            $record,
                            $data['points'],
                            $data['reason'],
                            $data['remark'] ?? null,
                            auth()->id()
                        );
                        \Filament\Notifications\Notification::make()
                            ->title('积分扣减成功')
                            ->success()
                            ->send();
                    } catch (\InvalidArgumentException $e) {
                        \Filament\Notifications\Notification::make()
                            ->title('积分扣减失败')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            Action::make('退还积分')
                ->label('退还积分')
                ->icon('heroicon-o-arrow-uturn-left')
                ->color('warning')
                ->form([
                    TextInput::make('points')
                        ->label('积分数量')
                        ->required()
                        ->numeric()
                        ->minValue(1),
                    Select::make('reason')
                        ->label('原因')
                        ->required()
                        ->options(PointTransactionReason::options()),
                    Textarea::make('remark')
                        ->label('备注')
                        ->nullable(),
                ])
                ->action(function (Member $record, array $data) {
                    $service = app(PointAdjustmentService::class);
                    $service->refund(
                        $record,
                        $data['points'],
                        $data['reason'],
                        $data['remark'] ?? null,
                        auth()->id()
                    );
                    \Filament\Notifications\Notification::make()
                        ->title('积分退还成功')
                        ->success()
                        ->send();
                }),
            Action::make('冻结积分')
                ->label('冻结积分')
                ->icon('heroicon-o-pause-circle')
                ->color('gray')
                ->form([
                    TextInput::make('points')
                        ->label('积分数量')
                        ->required()
                        ->numeric()
                        ->minValue(1),
                    Select::make('reason')
                        ->label('原因')
                        ->required()
                        ->options(PointTransactionReason::options()),
                    Textarea::make('remark')
                        ->label('备注')
                        ->nullable(),
                ])
                ->action(function (Member $record, array $data) {
                    $service = app(PointAdjustmentService::class);
                    try {
                        $service->freeze(
                            $record,
                            $data['points'],
                            $data['reason'],
                            $data['remark'] ?? null,
                            auth()->id()
                        );
                        \Filament\Notifications\Notification::make()
                            ->title('积分冻结成功')
                            ->success()
                            ->send();
                    } catch (\InvalidArgumentException $e) {
                        \Filament\Notifications\Notification::make()
                            ->title('积分冻结失败')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            Action::make('解冻积分')
                ->label('解冻积分')
                ->icon('heroicon-o-play-circle')
                ->color('info')
                ->form([
                    TextInput::make('points')
                        ->label('积分数量')
                        ->required()
                        ->numeric()
                        ->minValue(1),
                    Select::make('reason')
                        ->label('原因')
                        ->required()
                        ->options(PointTransactionReason::options()),
                    Textarea::make('remark')
                        ->label('备注')
                        ->nullable(),
                ])
                ->action(function (Member $record, array $data) {
                    $service = app(PointAdjustmentService::class);
                    try {
                        $service->unfreeze(
                            $record,
                            $data['points'],
                            $data['reason'],
                            $data['remark'] ?? null,
                            auth()->id()
                        );
                        \Filament\Notifications\Notification::make()
                            ->title('积分解冻成功')
                            ->success()
                            ->send();
                    } catch (\InvalidArgumentException $e) {
                        \Filament\Notifications\Notification::make()
                            ->title('积分解冻失败')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }
}

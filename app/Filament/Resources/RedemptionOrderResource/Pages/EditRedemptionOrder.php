<?php

namespace App\Filament\Resources\RedemptionOrderResource\Pages;

use App\Filament\Resources\RedemptionOrderResource;
use Filament\Resources\Pages\EditRecord;

class EditRedemptionOrder extends EditRecord
{
    protected static string $resource = RedemptionOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\DeleteAction::make(),
        ];
    }
}
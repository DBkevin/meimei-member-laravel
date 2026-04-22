<?php

namespace App\Filament\Resources\RedemptionOrderResource\Pages;

use App\Filament\Resources\RedemptionOrderResource;
use Filament\Resources\Pages\ViewRecord;

class ViewRedemptionOrder extends ViewRecord
{
    protected static string $resource = RedemptionOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\EditAction::make(),
        ];
    }
}
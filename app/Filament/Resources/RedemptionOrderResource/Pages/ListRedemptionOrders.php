<?php

namespace App\Filament\Resources\RedemptionOrderResource\Pages;

use App\Filament\Resources\RedemptionOrderResource;
use Filament\Resources\Pages\ListRecords;

class ListRedemptionOrders extends ListRecords
{
    protected static string $resource = RedemptionOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make(),
        ];
    }
}
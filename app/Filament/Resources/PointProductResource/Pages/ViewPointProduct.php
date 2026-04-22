<?php

namespace App\Filament\Resources\PointProductResource\Pages;

use App\Filament\Resources\PointProductResource;
use Filament\Resources\Pages\ViewRecord;

class ViewPointProduct extends ViewRecord
{
    protected static string $resource = PointProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\EditAction::make(),
        ];
    }
}
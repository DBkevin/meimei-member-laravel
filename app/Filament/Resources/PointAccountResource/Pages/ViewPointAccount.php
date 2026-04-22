<?php

namespace App\Filament\Resources\PointAccountResource\Pages;

use App\Filament\Resources\PointAccountResource;
use Filament\Resources\Pages\ViewRecord;

class ViewPointAccount extends ViewRecord
{
    protected static string $resource = PointAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\EditAction::make(),
        ];
    }
}
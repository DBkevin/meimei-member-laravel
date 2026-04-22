<?php

namespace App\Filament\Resources\ShowcaseResource\Pages;

use App\Filament\Resources\ShowcaseResource;
use Filament\Resources\Pages\ViewRecord;

class ViewShowcase extends ViewRecord
{
    protected static string $resource = ShowcaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\EditAction::make(),
        ];
    }
}
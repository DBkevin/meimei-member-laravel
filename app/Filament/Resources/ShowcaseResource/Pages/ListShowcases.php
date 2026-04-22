<?php

namespace App\Filament\Resources\ShowcaseResource\Pages;

use App\Filament\Resources\ShowcaseResource;
use Filament\Resources\Pages\ListRecords;

class ListShowcases extends ListRecords
{
    protected static string $resource = ShowcaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make(),
        ];
    }
}
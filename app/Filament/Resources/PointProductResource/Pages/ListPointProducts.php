<?php

namespace App\Filament\Resources\PointProductResource\Pages;

use App\Filament\Resources\PointProductResource;
use Filament\Resources\Pages\ListRecords;

class ListPointProducts extends ListRecords
{
    protected static string $resource = PointProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make(),
        ];
    }
}
<?php

namespace App\Filament\Resources\PointProductResource\Pages;

use App\Filament\Resources\PointProductResource;
use Filament\Resources\Pages\EditRecord;

class EditPointProduct extends EditRecord
{
    protected static string $resource = PointProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\DeleteAction::make(),
        ];
    }
}
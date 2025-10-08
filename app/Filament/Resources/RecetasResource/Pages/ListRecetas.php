<?php

namespace App\Filament\Resources\RecetasResource\Pages;

use App\Filament\Resources\RecetasResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRecetas extends ListRecords
{
    protected static string $resource = RecetasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

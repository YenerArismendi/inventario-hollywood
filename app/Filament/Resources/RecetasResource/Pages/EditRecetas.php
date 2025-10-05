<?php

namespace App\Filament\Resources\RecetasResource\Pages;

use App\Filament\Resources\RecetasResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRecetas extends EditRecord
{
    protected static string $resource = RecetasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

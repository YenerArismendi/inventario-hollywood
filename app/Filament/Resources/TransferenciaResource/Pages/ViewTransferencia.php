<?php

namespace App\Filament\Resources\TransferenciaResource\Pages;

use App\Filament\Resources\TransferenciaResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTransferencia extends ViewRecord
{
    protected static string $resource = TransferenciaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}

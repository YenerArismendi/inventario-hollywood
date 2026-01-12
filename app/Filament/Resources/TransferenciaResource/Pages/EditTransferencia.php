<?php

namespace App\Filament\Resources\TransferenciaResource\Pages;

use App\Filament\Resources\TransferenciaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTransferencia extends EditRecord
{
    protected static string $resource = TransferenciaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}

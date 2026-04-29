<?php

namespace App\Filament\Resources\CompraProductoResource\Pages;

use App\Filament\Resources\CompraProductoResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Pages\Actions;

class ViewCompraProducto extends ViewRecord
{
    protected static string $resource = CompraProductoResource::class;

    public function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [];
    }
}

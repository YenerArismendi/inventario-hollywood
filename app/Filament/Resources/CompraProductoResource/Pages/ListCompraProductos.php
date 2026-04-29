<?php

namespace App\Filament\Resources\CompraProductoResource\Pages;

use App\Filament\Resources\CompraProductoResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListCompraProductos extends ListRecords
{
    protected static string $resource = CompraProductoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

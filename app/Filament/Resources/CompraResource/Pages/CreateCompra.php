<?php

namespace App\Filament\Resources\CompraResource\Pages;

use App\Filament\Resources\CompraResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Insumo;

class CreateCompra extends CreateRecord
{
    protected static string $resource = CompraResource::class;


    protected function getFormActions(): array
    {
        return []; // Esto quita el botón “Crear” de Filament
    }
}

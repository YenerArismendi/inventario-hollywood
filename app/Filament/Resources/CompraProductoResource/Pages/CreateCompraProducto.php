<?php

namespace App\Filament\Resources\CompraProductoResource\Pages;

use App\Filament\Resources\CompraProductoResource;
use Filament\Resources\Pages\CreateRecord;
use App\Services\CompraService;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;

class CreateCompraProducto extends CreateRecord
{
    protected static string $resource = CompraProductoResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        // Usar el servicio para registrar la compra y actualizar stock
        $service = app(CompraService::class);
        $compra = $service->registrarCompra($data);
        Notification::make()
            ->title('Compra registrada y stock actualizado')
            ->success()
            ->send();
        return $compra;
    }
}

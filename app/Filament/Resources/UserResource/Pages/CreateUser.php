<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\QueryException;
use Filament\Notifications\Notification;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        try {
            // Usa el método padre para crear el registro correctamente
            return parent::handleRecordCreation($data);
        } catch (QueryException $e) {
            if ($e->errorInfo[1] === 1062) {
                Notification::make()
                    ->title('Dato duplicado')
                    ->body('El documento de identidad ya existe en el sistema.')
                    ->danger()
                    ->send();

                // Detiene la ejecución sin romper la UI
                $this->halt();

                // Devuelve un modelo vacío para que Filament no falle
                return $this->getModel()::make();
            }

            throw $e;
        }
    }
}

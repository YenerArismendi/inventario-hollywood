<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\QueryException;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        try {
            $record->update($data);

            // --- NUEVA LÓGICA PARA active_bodega_id ---
            $bodegaIds = $record->bodegas()->select('bodegas.id')->pluck('id');

            if (!$record->active_bodega_id || ! $bodegaIds->contains($record->active_bodega_id)) {
                $record->active_bodega_id = $bodegaIds->first() ?? null;
                $record->saveQuietly(); // saveQuietly evita disparar otros observers
            }
            // --- FIN DE LA LÓGICA ---

            return $record;
        } catch (QueryException $e) {
            if ($e->errorInfo[1] === 1062) {
                Notification::make()
                    ->title('Dato duplicado')
                    ->body('El documento de identidad ya existe en el sistema.')
                    ->danger()
                    ->send();

                $this->halt(); // Detiene la ejecución y evita que Filament lance el error por defecto

                return $record;
            }

            throw $e;
        }
    }

}

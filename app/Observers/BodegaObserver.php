<?php

namespace App\Observers;

use App\Models\Bodega;
use App\Models\User;

class BodegaObserver
{
    /**
     * Handle the Bodega "created" event.
     *
     * Se dispara cuando se crea una nueva bodega.
     */
    public function created(Bodega $bodega): void
    {
        // Si se asignó un encargado al crear la bodega...
        if ($bodega->encargado_id) {
            // ...buscamos a ese usuario y actualizamos su bodega_id.
            User::find($bodega->encargado_id)?->update(['bodega_id' => $bodega->id]);
        }
    }

    /**
     * Handle the Bodega "updated" event.
     *
     * Se dispara cuando se actualiza una bodega.
     */
    public function updated(Bodega $bodega): void
    {
        // Verificamos si el campo 'encargado_id' ha cambiado.
        if ($bodega->isDirty('encargado_id')) {
            // Obtenemos el ID del encargado anterior.
            $oldEncargadoId = $bodega->getOriginal('encargado_id');

            // Si había un encargado anterior, le quitamos su bodega asignada.
            if ($oldEncargadoId) {
                User::find($oldEncargadoId)?->update(['bodega_id' => null]);
            }

            // Si se asignó un nuevo encargado, actualizamos su bodega_id.
            if ($bodega->encargado_id) {
                User::find($bodega->encargado_id)?->update(['bodega_id' => $bodega->id]);
            }
        }
    }

    /**
     * Handle the Bodega "deleted" event.
     */
    public function deleted(Bodega $bodega): void
    {
        //
    }

    /**
     * Handle the Bodega "restored" event.
     */
    public function restored(Bodega $bodega): void
    {
        //
    }

    /**
     * Handle the Bodega "force deleted" event.
     */
    public function forceDeleted(Bodega $bodega): void
    {
        //
    }
}

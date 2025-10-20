<?php

namespace App\Observers;

use App\Models\Bodega;
use App\Models\MovimientoInventario;
use Illuminate\Support\Facades\DB;

class MovimientoInventarioObserver
{
    /**
     * Handle the MovimientoInventario "created" event.
     */
    public function created(MovimientoInventario $movimiento): void
    {
        //
    }

    /**
     * Handle the MovimientoInventario "updated" event.
     * Se dispara cuando un movimiento es actualizado.
     */
    public function updated(MovimientoInventario $movimiento): void
    {
        // Nos aseguramos de que la lógica solo se ejecute si el estado ha cambiado a 'confirmado'
        if ($movimiento->isDirty('estado') && $movimiento->estado === 'confirmado') {

            // Usamos una transacción para asegurar la integridad de los datos.
            DB::transaction(function () use ($movimiento) {
                // Obtenemos la bodega y usamos la relación para actualizar el stock.
                $bodega = Bodega::find($movimiento->bodega_id);
                if ($bodega) {
                    // Verificamos si el artículo ya está en la bodega
                    if ($bodega->articles()->where('article_id', $movimiento->article_id)->exists()) {
                        // Si existe, incrementamos el stock
                        $bodega->articles()->updateExistingPivot($movimiento->article_id, [
                            'stock' => DB::raw("stock + {$movimiento->cantidad}")
                        ]);
                    } else {
                        // Si no existe, lo adjuntamos con el stock inicial
                        $bodega->articles()->attach($movimiento->article_id, ['stock' => $movimiento->cantidad]);
                    }
                }
            });
        }
    }

    /**
     * Handle the MovimientoInventario "deleted" event.
     */
    public function deleted(MovimientoInventario $movimiento): void
    {
        //
    }

    /**
     * Handle the MovimientoInventario "restored" event.
     */
    public function restored(MovimientoInventario $movimiento): void
    {
        //
    }

    /**
     * Handle the MovimientoInventario "force deleted" event.
     */
    public function forceDeleted(MovimientoInventario $movimiento): void
    {
        //
    }
}

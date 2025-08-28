<?php

namespace App\Observers;

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
            // Si algo falla, todo se revierte.
            DB::transaction(function () use ($movimiento) {
                $inventario = DB::table('bodega_producto')
                    ->where('bodega_id', $movimiento->bodega_id)
                    ->where('producto_id', $movimiento->producto_id);

                // Si ya existe un registro de inventario para este producto en esta bodega, lo incrementamos.
                // Si no, creamos el registro con la cantidad del movimiento.
                $inventario->exists()
                    ? $inventario->increment('cantidad', $movimiento->cantidad)
                    : DB::table('bodega_producto')->insert([
                    'bodega_id' => $movimiento->bodega_id,
                    'producto_id' => $movimiento->producto_id,
                    'cantidad' => $movimiento->cantidad,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
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

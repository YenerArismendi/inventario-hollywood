<?php

namespace App\Services;

use App\Models\Transferencia;
use App\Models\Insumo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;

class TransferenciaService
{
    /**
     * Procesa el despacho de una transferencia.
     * Descuenta el stock de la bodega de origen.
     */
    public function despachar(Transferencia $transferencia)
    {
        if ($transferencia->estado !== 'borrador') {
            throw new Exception("Solo se pueden despachar transferencias en estado borrador.");
        }

        return DB::transaction(function () use ($transferencia) {
            $transferencia->load('detalles');

            foreach ($transferencia->detalles as $detalle) {
                $tabla = $detalle->article_id ? 'bodega_article' : 'bodega_insumo';
                $foreignKey = $detalle->article_id ? 'article_id' : 'insumo_id';
                $idRelacionado = $detalle->article_id ?? $detalle->insumo_id;

                $pivot = DB::table($tabla)
                    ->where('bodega_id', $transferencia->bodega_origen_id)
                    ->where($foreignKey, $idRelacionado)
                    ->first();

                if (!$pivot || $pivot->stock < $detalle->cantidad_enviada) {
                    $nombre = $detalle->article_id ? "Artículo ID: {$idRelacionado}" : "Insumo ID: {$idRelacionado}";
                    throw new Exception("Stock insuficiente en la bodega de origen para: {$nombre}");
                }

                DB::table($tabla)
                    ->where('id', $pivot->id)
                    ->decrement('stock', $detalle->cantidad_enviada);

                // Si es insumo, actualizar también el stock global consolidado
                if ($detalle->insumo_id) {
                    $nuevoStockGlobal = DB::table('bodega_insumo')
                        ->where('insumo_id', $detalle->insumo_id)
                        ->sum('stock');
                    Insumo::where('id', $detalle->insumo_id)->update(['stock' => $nuevoStockGlobal]);
                }
            }

            $transferencia->update([
                'estado' => 'despachado',
                'fecha_despacho' => now(),
            ]);

            return true;
        });
    }

    /**
     * Procesa la recepción de una transferencia.
     * Si es conforme, suma el stock a la bodega de destino.
     */
    public function recibir(Transferencia $transferencia, array $datosRecepcion, bool $conforme)
    {
        if ($transferencia->estado !== 'despachado') {
            throw new Exception("Solo se pueden recibir transferencias despachadas.");
        }

        return DB::transaction(function () use ($transferencia, $datosRecepcion, $conforme) {
            foreach ($datosRecepcion['detalles'] as $idDetalle => $cantidad) {
                DB::table('transferencia_detalles')
                    ->where('id', $idDetalle)
                    ->update(['cantidad_recibida' => $cantidad]);
            }

            $transferencia->update([
                'user_recepcion_id' => Auth::id(),
                'evidencia_recepcion' => $datosRecepcion['evidencia'] ?? null,
                'observaciones_recepcion' => $datosRecepcion['observaciones'] ?? null,
                'fecha_recepcion' => now(),
                'estado' => $conforme ? 'completado' : 'en_disputa',
            ]);

            if ($conforme) {
                $this->cargarStockDestino($transferencia);
            }

            return true;
        });
    }

    /**
     * Acción del administrador para aprobar una transferencia en disputa.
     */
    public function aprobarDisputa(Transferencia $transferencia)
    {
        if ($transferencia->estado !== 'en_disputa') {
            throw new Exception("Solo se pueden aprobar transferencias en disputa.");
        }

        return DB::transaction(function () use ($transferencia) {
            $this->cargarStockDestino($transferencia);

            $transferencia->update([
                'estado' => 'completado',
            ]);

            return true;
        });
    }

    /**
     * Suma las cantidades recibidas a la bodega de destino.
     */
    protected function cargarStockDestino(Transferencia $transferencia)
    {
        $transferencia->load('detalles');

        foreach ($transferencia->detalles as $detalle) {
            $cantidad = $detalle->cantidad_recibida ?? $detalle->cantidad_enviada;
            $tabla = $detalle->article_id ? 'bodega_article' : 'bodega_insumo';
            $foreignKey = $detalle->article_id ? 'article_id' : 'insumo_id';
            $idRelacionado = $detalle->article_id ?? $detalle->insumo_id;

            $pivot = DB::table($tabla)
                ->where('bodega_id', $transferencia->bodega_destino_id)
                ->where($foreignKey, $idRelacionado)
                ->first();

            if ($pivot) {
                DB::table($tabla)
                    ->where('id', $pivot->id)
                    ->increment('stock', $cantidad);
            } else {
                DB::table($tabla)->insert([
                    'bodega_id' => $transferencia->bodega_destino_id,
                    $foreignKey => $idRelacionado,
                    'stock' => $cantidad,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Si es insumo, actualizar también el stock global consolidado
            if ($detalle->insumo_id) {
                $nuevoStockGlobal = DB::table('bodega_insumo')
                    ->where('insumo_id', $detalle->insumo_id)
                    ->sum('stock');
                Insumo::where('id', $detalle->insumo_id)->update(['stock' => $nuevoStockGlobal]);
            }
        }
    }
}

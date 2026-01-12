<?php

namespace App\Services;

use App\Models\Recetas;
use App\Models\Insumo;
use App\Models\Article;
use Illuminate\Support\Facades\DB;
use Exception;

class ProduccionService
{
    /**
     * Procesa la producción de una receta.
     * 
     * @param Recetas $receta
     * @param int $cantidadProduccion
     * @return bool
     * @throws Exception
     */
    public function producir(Recetas $receta, int $cantidadProduccion)
    {
        if (!$receta->article_id) {
            throw new Exception("La receta no tiene un producto terminado vinculado.");
        }

        return DB::transaction(function () use ($receta, $cantidadProduccion) {
            // Cargar detalles e insumos si no están cargados
            $receta->load('detalles.insumo');

            // 1. Validar y descontar stock de insumos en la bodega específica
            $bodegaProduccionId = $receta->bodega_id ?? 1;

            foreach ($receta->detalles as $detalle) {
                $insumo = $detalle->insumo;
                $cantidadRequerida = $detalle->cantidad * $cantidadProduccion;

                $pivotInsumo = DB::table('bodega_insumo')
                    ->where('bodega_id', $bodegaProduccionId)
                    ->where('insumo_id', $insumo->id)
                    ->first();

                if (!$pivotInsumo || $pivotInsumo->stock < $cantidadRequerida) {
                    throw new Exception("Stock insuficiente del insumo '{$insumo->nombre}' en la bodega de producción.");
                }

                // Descontar de la bodega
                DB::table('bodega_insumo')
                    ->where('id', $pivotInsumo->id)
                    ->decrement('stock', $cantidadRequerida);

                // Actualizar stock global del insumo (para reportes locales)
                $nuevoStockGlobal = DB::table('bodega_insumo')
                    ->where('insumo_id', $insumo->id)
                    ->sum('stock');
                $insumo->update(['stock' => $nuevoStockGlobal]);
            }

            // 3. Aumentar stock del producto terminado
            $article = $receta->article;
            $bodegaId = $receta->bodega_id ?? 1; // Usar la bodega configurada en la receta o la bodega principal por defecto

            $pivot = DB::table('bodega_article')
                ->where('article_id', $article->id)
                ->where('bodega_id', $bodegaId)
                ->first();

            if ($pivot) {
                DB::table('bodega_article')
                    ->where('article_id', $article->id)
                    ->where('bodega_id', $bodegaId)
                    ->increment('stock', $cantidadProduccion);
            } else {
                DB::table('bodega_article')->insert([
                    'article_id' => $article->id,
                    'bodega_id' => $bodegaId,
                    'stock' => $cantidadProduccion,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return true;
        });
    }
}

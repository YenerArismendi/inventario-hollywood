<?php

namespace App\Services;

use App\Models\Compra;
use App\Models\CompraDetalles;
use App\Models\Article;
use Illuminate\Support\Facades\DB;
use Exception;

class CompraService
{
    /**
     * Registra una compra de productos terminados y actualiza el stock en la bodega.
     *
     * @param array $data [
     *   'proveedor_id' => int,
     *   'bodega_id' => int,
     *   'articulos' => [
     *     ['article_id' => int, 'cantidad' => int, 'costo_unitario' => float]
     *   ]
     * ]
     * @return Compra
     * @throws Exception
     */
    public function registrarCompra(array $data): Compra
    {
        return DB::transaction(function () use ($data) {
            // Crear la compra
            $compra = Compra::create([
                'proveedor_id' => $data['proveedor_id'],
                'bodega_id' => $data['bodega_id'],
                'fecha' => now(),
                'total' => 0,
            ]);

            $total = 0;

            foreach ($data['articulos'] as $item) {
                $article = Article::findOrFail($item['article_id']);
                $cantidad = $item['cantidad'];
                $costo_unitario = $item['costo_unitario'];
                $costo_total = $cantidad * $costo_unitario;
                $total += $costo_total;

                // Crear detalle
                CompraDetalles::create([
                    'compra_id' => $compra->id,
                    'article_id' => $article->id,
                    'cantidad' => $cantidad,
                    'costo_unitario' => $costo_unitario,
                    'costo_total' => $costo_total,
                ]);

                // Actualizar stock en la bodega
                $pivot = DB::table('bodega_article')
                    ->where('bodega_id', $data['bodega_id'])
                    ->where('article_id', $article->id)
                    ->first();

                if ($pivot) {
                    DB::table('bodega_article')
                        ->where('id', $pivot->id)
                        ->increment('stock', $cantidad);
                } else {
                    DB::table('bodega_article')->insert([
                        'bodega_id' => $data['bodega_id'],
                        'article_id' => $article->id,
                        'stock' => $cantidad,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // Actualizar total de la compra
            $compra->update(['total' => $total]);

            return $compra;
        });
    }
}


<?php

namespace App\Observers;

use App\Mail\AlertaStockBajo;
use App\Models\BodegaArticle;
use Illuminate\Support\Facades\Mail;

class BodegaArticleObserver
{
    /**
     * Handle the BodegaArticle "updated" event.
     * Se dispara cada vez que se actualiza el stock de un artículo en una bodega.
     */
    public function updated(BodegaArticle $bodegaArticle): void
    {
        $umbralStockBajo = 100;
        $stockAnterior = $bodegaArticle->getOriginal('stock');
        $nuevoStock = $bodegaArticle->fresh()->stock;

        // Esto evita enviar correos repetidos si el stock ya era bajo.
        $cruzoElUmbral = ($stockAnterior >= $umbralStockBajo) && ($nuevoStock < $umbralStockBajo);

        if ($cruzoElUmbral) {

            // Comprobamos si el nuevo stock está por debajo del umbral (doble chequeo por seguridad).
            if ($nuevoStock !== null) {
                // Cargamos las relaciones necesarias de forma eficiente.
                $bodegaArticle->load(['article', 'bodega.encargado']);

                $article = $bodegaArticle->article;
                $bodega = $bodegaArticle->bodega;

                $destinatarios = collect();

                // 1. Añadimos el correo del encargado, si y solo si existe.
                $destinatarios->push($bodega->encargado?->email);

                // 2. Añadimos el correo central de alertas.
                $destinatarios->push(config('mail.alerts_to'));

                // 3. Limpiamos la lista: eliminamos nulos, vacíos y duplicados.
                $destinatariosLimpios = $destinatarios->filter()->unique()->all();

                // Si hay destinatarios, enviamos el correo.
                if (!empty($destinatariosLimpios)) {
                    Mail::to($destinatariosLimpios)
                        ->send(new AlertaStockBajo($article, $bodega, $nuevoStock));
                }
            }
        }
    }
}

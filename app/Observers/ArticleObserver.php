<?php

namespace App\Observers;

use App\Models\Article;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ArticleObserver
{
    /**
     * Handle the Article "created" event.
     */
    public function created(Article $article): void
    {
        // Asegurarnos de que el artículo tiene un código
        if (empty($article->codigo)) {
            return;
        }

        // Generar el contenido del QR (el código único del producto)
        $qrContent = $article->codigo;

        // Definir la ruta donde se guardará el QR
        $filePath = 'qrcodes/' . $article->codigo . '.svg';

        // Generar el QR en formato SVG (más ligero y escalable)
        $qrImage = QrCode::format('svg')->size(200)->generate($qrContent);

        // Guardar el archivo en el disco público
        Storage::disk('public')->put($filePath, $qrImage);

        // Actualizar el modelo con la ruta al QR sin disparar más eventos
        $article->codigo_qr = $filePath;
        $article->saveQuietly();
    }

    /**
     * Handle the Article "updated" event.
     */
    public function updated(Article $article): void
    {
        //
    }

    /**
     * Handle the Article "deleted" event.
     */
    public function deleted(Article $article): void
    {
        //
    }

    /**
     * Handle the Article "restored" event.
     */
    public function restored(Article $article): void
    {
        //
    }

    /**
     * Handle the Article "force deleted" event.
     */
    public function forceDeleted(Article $article): void
    {
        //
    }
}

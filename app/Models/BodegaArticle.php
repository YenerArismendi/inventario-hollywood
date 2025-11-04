<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Representa la relación de inventario entre un Artículo y una Bodega.
 *
 * @property int $article_id
 * @property int $bodega_id
 * @property int $stock
 * @property string|null $columna
 * @property int|null $fila
 */
class BodegaArticle extends Pivot
{
    // Indicamos explícitamente el nombre de la tabla.
    protected $table = 'bodega_article';

    // Hacemos que el modelo sepa que tiene sus propias relaciones.
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    public function bodega(): BelongsTo
    {
        return $this->belongsTo(Bodega::class);
    }
}

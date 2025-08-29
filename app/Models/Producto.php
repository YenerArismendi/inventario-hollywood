<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'sku', 'descripcion', 'precio_venta'];

    /**
     * Un Producto puede estar en muchas Bodegas.
     */
    public function bodegas(): BelongsToMany
    {
        return $this->belongsToMany(Bodega::class, 'bodega_producto')->withPivot('cantidad')->withTimestamps();
    }
}

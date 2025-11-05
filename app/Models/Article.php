<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'articles';
    protected $primaryKey = 'id';
    protected $fillable = [
        'nombre', 'presentation', 'codigo', 'codigo_barras', 'descripcion', 'costo', 'precio_venta',
        'unidad_medida', 'imagen', 'codigo_qr', 'estado', 'temporada',
        'category_id', 'brand_id', 'proveedor_id'
    ];

    // Relación con proveedor
    public function proveedor(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Suppliers::class, 'proveedor_id');
    }


    // Relación con Categoría
    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // Relación con Marca
    public function brand(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Un artículo puede estar en muchas bodegas, con un stock específico en cada una.
     */
    public function bodegas(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Bodega::class, 'bodega_article')
            ->using(BodegaArticle::class)
            ->as('pivot')
            ->withPivot('stock')
            ->withTimestamps();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Article extends Model
{
    use HasFactory;

    protected $table = 'articles';
    protected $primaryKey = 'id';
    protected $fillable = ['nombre', 'tipo', 'codigo', 'descripcion', 'precio', 'unidad_medida', 'imagen', 'estado', 'temporada', 'proveedor_id', 'tipo_detalle'];

    // Relación con proveedor
    public function proveedor()
    {
        return $this->belongsTo(Suppliers::class, 'proveedor_id');
    }


    public function variantes()
    {
        return $this->hasMany(Variante::class);
    }

    /**
     * Un artículo puede estar en muchas bodegas, con un stock específico en cada una.
     */
    public function bodegas()
    {
        return $this->belongsToMany(Bodega::class, 'bodega_article')
            ->using(BodegaArticle::class)
            ->as('pivot')
            ->withPivot('stock')
            ->withTimestamps();
    }
}

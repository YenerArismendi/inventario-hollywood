<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{

    protected $table = 'articles';
    protected $primaryKey = 'id';
    protected $fillable = ['nombre', 'tipo', 'codigo', 'descripcion', 'precio', 'unidad_medida', 'imagen', 'estado', 'temporada', 'proveedor_id', 'bodega_id'];
    // Relación con proveedor
    public function proveedor()
    {
        return $this->belongsTo(Suppliers::class, 'proveedor_id');
    }

    public function variantes()
    {
        return $this->hasMany(Variante::class);
    }

//    Relacion para asignar un articulo a cada bodega
    public function bodega()
    {
        return $this->belongsTo(Bodega::class);
    }

}

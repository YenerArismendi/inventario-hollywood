<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{

    protected $table = 'articles';
    protected $primaryKey = 'id';
    protected $fillable = ['nombre', 'tipo', 'codigo', 'descripcion', 'precio', 'unidad_medida', 'imagen', 'estado', 'proveedor_id'];
    // Relación con proveedor
    public function proveedor()
    {
        return $this->belongsTo(Suppliers::class, 'id');
    }
}

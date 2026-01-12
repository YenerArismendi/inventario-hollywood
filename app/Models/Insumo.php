<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Insumo extends Model
{
    protected $table = 'insumos';
    protected $primaryKey = 'id';
    protected $fillable = ['nombre', 'unidad_compra', 'unidad_consumo', 'conversion', 'stock', 'costo_unitario_promedio'];

    public function bodegas()
    {
        return $this->belongsToMany(Bodega::class, 'bodega_insumo')
            ->withPivot('stock', 'costo_unitario_promedio')
            ->withTimestamps();
    }
}

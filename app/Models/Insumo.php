<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Insumo extends Model
{
    Protected $table = 'insumos';
    Protected $primaryKey = 'id';
    Protected $fillable = ['nombre', 'unidad_compra', 'unidad_consumo', 'conversion', 'stock', 'costo_unitario_promedio'];
}

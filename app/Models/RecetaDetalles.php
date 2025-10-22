<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecetaDetalles extends Model
{

    protected $table = 'receta_detalles';
    protected $primaryKey = 'id';
    protected $fillable = ['receta_id', 'insumos_id', 'cantidad', 'unidad'];


    public function receta()
    {
        return $this->belongsTo(Recetas::class, 'receta_id');
    }

    public function insumo()
    {
        return $this->belongsTo(Insumo::class, 'insumos_id');
    }

}

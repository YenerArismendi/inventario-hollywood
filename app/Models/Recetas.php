<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recetas extends Model
{
    Protected $table = 'recetas';
    Protected $fillable = ['nombre', 'descripcion', 'articulo_final_id', 'tipo'];

    protected $casts = [
        'detalles' => 'array',
    ];

    public function detalles()
    {
        return $this->hasMany(RecetaDetalles::class, 'receta_id');
    }

    public function articuloFinal()
    {
        return $this->belongsTo(Article::class, 'articulo_final_id');
    }

    protected static function booted()
    {
        static::saving(function ($receta) {
            $receta->costo_unitario = $receta->detalles->sum('costo_total') / max($receta->rendimiento, 1);
        });
    }

}

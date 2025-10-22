<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recetas extends Model
{
    protected $table = 'recetas';
    protected $fillable = ['nombre', 'precio', 'tipo'];

    public function detalles()
    {
        return $this->hasMany(RecetaDetalles::class, 'receta_id');
    }

    public function getCostoTotalAttribute()
    {
        // Evitamos errores si no hay detalles o relaciones cargadas
        if (!$this->relationLoaded('detalles')) {
            $this->load('detalles.insumo');
        }
        
        // logger([
        //     'detalles' => $this->detalles,
        //     'insumos' => $this->detalles?->pluck('insumo'),
        // ]);

        // Si no hay detalles, devolvemos 0
        if (!$this->detalles || $this->detalles->isEmpty()) {
            return 0;
        }

        return $this->detalles->sum(function ($detalle) {
            $costoInsumo = $detalle->insumo->costo_unitario_promedio ?? 0;
            return $detalle->cantidad * $costoInsumo;
        });
    }
}

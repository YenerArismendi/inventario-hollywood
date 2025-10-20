<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompraDetalles extends Model
{
    protected $table = "compra_detalles";

    protected $fillable = [
        'compra_id',
        'insumo_id',
        'cantidad',          // Cantidad en unidad de compra
        'costo_unitario',    // Costo por unidad de compra
        'costo_total',
    ];

    public function compra()
    {
        return $this->belongsTo(\App\Models\Compra::class);
    }

    protected static function booted()
    {
        static::created(function ($detalle) {
            $insumo = $detalle->insumo;

            // 🔹 Conversión: cuántas unidades de consumo hay por unidad de compra
            $conversion = max((float)$insumo->conversion, 1);

            // 🔹 Calcular cantidad real en unidad de consumo
            $cantidadReal = $detalle->cantidad * $conversion;

            // 🔹 Calcular costo por unidad de consumo (lo que vale 1 unidad de consumo)
            $costoUnitarioReal = $detalle->costo_unitario / $conversion;

            // 🔹 Actualizar stock total (en unidad de consumo)
            $nuevoStock = $insumo->stock + $cantidadReal;

            // 🔹 Recalcular el costo promedio por unidad de consumo
            // Si ya hay stock anterior, pondera el nuevo costo
            $nuevoCostoPromedio = $insumo->stock > 0
                ? (($insumo->stock * $insumo->costo_unitario_promedio) + ($cantidadReal * $costoUnitarioReal)) / $nuevoStock
                : $costoUnitarioReal;

            // 🔹 Actualizar el insumo
            $insumo->update([
                'stock' => $nuevoStock,
                'costo_unitario_promedio' => $nuevoCostoPromedio,
            ]);
        });
    }


    public function insumo()
    {
        return $this->belongsTo(Insumo::class, 'insumo_id');
    }
}

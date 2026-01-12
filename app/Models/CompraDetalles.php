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
            $compra = $detalle->compra;
            $bodegaId = $compra->bodega_id;

            if (!$bodegaId) {
                return;
            }

            $conversion = max((float)$insumo->conversion, 1);
            $cantidadReal = $detalle->cantidad * $conversion;
            $costoUnitarioReal = $detalle->costo_unitario / $conversion;

            $pivot = \Illuminate\Support\Facades\DB::table('bodega_insumo')
                ->where('bodega_id', $bodegaId)
                ->where('insumo_id', $insumo->id)
                ->first();

            $stockActual = $pivot ? (float)$pivot->stock : 0;
            $costoPromedioActual = $pivot ? (float)$pivot->costo_unitario_promedio : 0;

            $nuevoStock = $stockActual + $cantidadReal;

            $nuevoCostoPromedio = $stockActual > 0
                ? (($stockActual * $costoPromedioActual) + ($cantidadReal * $costoUnitarioReal)) / $nuevoStock
                : $costoUnitarioReal;

            if ($pivot) {
                \Illuminate\Support\Facades\DB::table('bodega_insumo')
                    ->where('id', $pivot->id)
                    ->update([
                        'stock' => $nuevoStock,
                        'costo_unitario_promedio' => $nuevoCostoPromedio,
                        'updated_at' => now(),
                    ]);
            } else {
                \Illuminate\Support\Facades\DB::table('bodega_insumo')->insert([
                    'bodega_id' => $bodegaId,
                    'insumo_id' => $insumo->id,
                    'stock' => $nuevoStock,
                    'costo_unitario_promedio' => $nuevoCostoPromedio,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $totalStock = \Illuminate\Support\Facades\DB::table('bodega_insumo')
                ->where('insumo_id', $insumo->id)
                ->sum('stock');

            // Calcular costo global ponderado
            $valorTotalGlobal = \Illuminate\Support\Facades\DB::table('bodega_insumo')
                ->where('insumo_id', $insumo->id)
                ->sum(\Illuminate\Support\Facades\DB::raw('stock * costo_unitario_promedio'));

            $costoPromedioGlobal = $totalStock > 0 ? $valorTotalGlobal / $totalStock : 0;

            $insumo->update([
                'stock' => $totalStock,
                'costo_unitario_promedio' => $costoPromedioGlobal
            ]);
        });

        static::deleting(function ($detalle) {
            $insumo = $detalle->insumo;
            $compra = $detalle->compra;
            $bodegaId = $compra->bodega_id;

            if (!$bodegaId) return;

            $conversion = max((float)$insumo->conversion, 1);
            $cantidadReal = $detalle->cantidad * $conversion;
            $costoUnitarioReal = $detalle->costo_unitario / $conversion;

            $pivot = \Illuminate\Support\Facades\DB::table('bodega_insumo')
                ->where('bodega_id', $bodegaId)
                ->where('insumo_id', $insumo->id)
                ->first();

            if ($pivot) {
                $stockActual = (float)$pivot->stock;
                $costoPromedioActual = (float)$pivot->costo_unitario_promedio;

                $nuevoStock = max(0, $stockActual - $cantidadReal);

                if ($nuevoStock > 0) {
                    $costoTotalActual = $stockActual * $costoPromedioActual;
                    $costoCompraActual = $cantidadReal * $costoUnitarioReal;
                    $nuevoCostoPromedio = ($costoTotalActual - $costoCompraActual) / $nuevoStock;
                } else {
                    $nuevoCostoPromedio = 0;
                }

                \Illuminate\Support\Facades\DB::table('bodega_insumo')
                    ->where('id', $pivot->id)
                    ->update([
                        'stock' => $nuevoStock,
                        'costo_unitario_promedio' => max(0, $nuevoCostoPromedio),
                        'updated_at' => now(),
                    ]);

                // Actualizar stock global y costo promedio global
                $totalStock = \Illuminate\Support\Facades\DB::table('bodega_insumo')
                    ->where('insumo_id', $insumo->id)
                    ->sum('stock');

                $valorTotalGlobal = \Illuminate\Support\Facades\DB::table('bodega_insumo')
                    ->where('insumo_id', $insumo->id)
                    ->sum(\Illuminate\Support\Facades\DB::raw('stock * costo_unitario_promedio'));

                $costoPromedioGlobal = $totalStock > 0 ? $valorTotalGlobal / $totalStock : 0;

                $insumo->update([
                    'stock' => $totalStock,
                    'costo_unitario_promedio' => $costoPromedioGlobal
                ]);
            }
        });
    }

    public function insumo()
    {
        return $this->belongsTo(Insumo::class, 'insumo_id');
    }
}

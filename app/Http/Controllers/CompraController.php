<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Compra;
use App\Models\CompraDetalles;
use App\Models\Insumo;
use Illuminate\Support\Facades\DB;

class CompraController extends Controller
{
    /**
     * Mostrar formulario de nueva compra o edición
     */
    public function form($id = null)
    {
        $insumos = Insumo::select('id', 'nombre')->get();
        $compra = null;
        $detalles = [];

        if ($id) {
            $compra = Compra::with('detalles.insumo')->findOrFail($id);

            $detalles = $compra->detalles->map(function ($d) {
                return [
                    'insumo_id' => $d->insumo_id,
                    'nombre' => $d->insumo->nombre ?? '',
                    'cantidad' => $d->cantidad,
                    'costo_unitario' => $d->costo_unitario,
                    'costo_total' => $d->costo_total,
                ];
            })->values()->toArray();
        }

        $insumosJson = json_encode($insumos, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
        $detallesJson = json_encode($detalles, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);

        return view('compras.form', [
            'insumosJson' => $insumosJson,
            'compra' => $compra,
            'detallesJson' => $detallesJson,
        ]);
    }

    /**
     * Guardar una nueva compra
     */
    public function store(Request $request)
    {
        $request->validate([
            'fecha' => 'required|date',
            'total' => 'required|numeric|min:0',
            'detalles' => 'required|array|min:1',
            'detalles.*.cantidad' => 'required|numeric|min:0.01',
            'detalles.*.costo_unitario' => 'required|numeric|min:0',
            'detalles.*.costo_total' => 'required|numeric|min:0',
        ]);

        $compra = Compra::create([
            'proveedor_id' => $request->proveedor_id,
            'fecha' => $request->fecha,
            'total' => $request->total,
        ]);

        foreach ($request->detalles as $detalle) {
            $compra->detalles()->create([
                'insumo_id' => $detalle['insumo_id'],
                'cantidad' => $detalle['cantidad'],
                'costo_unitario' => $detalle['costo_unitario'],
                'costo_total' => $detalle['costo_total'],
            ]);
        }
        return response()->json(['success' => true, 'compra_id' => $compra->id, 'message' => 'Compra registrada correctamente',]);
    }

    /**
     * Actualizar una compra existente
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'fecha' => 'required|date',
            'detalles' => 'required|array|min:1',
            'detalles.*.cantidad' => 'required|numeric|min:0.01',
            'detalles.*.costo_unitario' => 'required|numeric|min:0',
            'detalles.*.costo_total' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $compra = Compra::findOrFail($id);
            $total = collect($request->detalles)->sum('costo_total');

            $compra->update([
                'fecha' => $request->fecha,
                'total' => $total,
            ]);

            $compra->detalles()->delete();

            foreach ($request->detalles as $detalle) {
                $compra->detalles()->create([
                    'insumo_id' => $detalle['insumo_id'],
                    'cantidad' => $detalle['cantidad'],
                    'costo_unitario' => $detalle['costo_unitario'],
                    'costo_total' => $detalle['costo_total'],
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Compra actualizada correctamente',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}

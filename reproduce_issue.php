<?php

use App\Models\Compra;
use App\Models\Insumo;
use App\Models\Bodega;
use App\Models\Suppliers;
use Illuminate\Support\Facades\DB;

// 1. Setup Data
$bodega = Bodega::where('tipo', 'preparacion')->first();
if (!$bodega) {
    $bodega = Bodega::create([
        'nombre' => 'Bodega Test',
        'ubicacion' => 'Test',
        'tipo' => 'preparacion',
        'responsable' => 'Admin'
    ]);
}

$proveedor = Suppliers::first();
if (!$proveedor) {
    $proveedor = Suppliers::create(['name' => 'Proveedor Test']);
}

$insumo = Insumo::create([
    'nombre' => 'Insumo Test ' . uniqid(),
    'unidad_compra' => 'unidad',
    'unidad_consumo' => 'unidad',
    'conversion' => 1,
    'stock' => 0
]);

echo "Insumo creado: {$insumo->id}\n";
echo "Bodega usada: {$bodega->id}\n";

// Ensure pivot starts at 0
DB::table('bodega_insumo')->updateOrInsert(
    ['bodega_id' => $bodega->id, 'insumo_id' => $insumo->id],
    ['stock' => 0, 'costo_unitario_promedio' => 0]
);

// 2. Create Compra
echo "Creando Compra...\n";
$compra = Compra::create([
    'proveedor_id' => $proveedor->id,
    'bodega_id' => $bodega->id,
    'fecha' => now(),
    'total' => 1000
]);

echo "Compra creada: {$compra->id} (Bodega ID: {$compra->bodega_id})\n";

// 3. Create Detalle
echo "Creando Detalle...\n";
$detalle = $compra->detalles()->create([
    'insumo_id' => $insumo->id,
    'cantidad' => 10,
    'costo_unitario' => 100, // Costo unitario 100
    'costo_total' => 1000
]);

echo "Detalle creado: {$detalle->id}\n";

// 4. Verify Pivot and Global
$pivot = DB::table('bodega_insumo')
    ->where('bodega_id', $bodega->id)
    ->where('insumo_id', $insumo->id)
    ->first();

$insumo->refresh();

echo "--- Resultados ---\n";
echo "Stock Bodega esperado: 10\n";
echo "Stock Bodega actual: " . ($pivot ? $pivot->stock : 'N/A') . "\n";
echo "Costo Prom. Bodega esperado: 100\n";
echo "Costo Prom. Bodega actual: " . ($pivot ? $pivot->costo_unitario_promedio : 'N/A') . "\n";
echo "Stock Global esperado: 10\n";
echo "Stock Global actual: " . $insumo->stock . "\n";
echo "Costo Prom. Global esperado: 100\n";
echo "Costo Prom. Global actual: " . $insumo->costo_unitario_promedio . "\n";

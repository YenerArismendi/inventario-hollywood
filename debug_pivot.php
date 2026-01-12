<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Bodega;
use App\Models\Insumo;

// 1. Create or Find a Prep Bodega
$bodega = Bodega::firstOrCreate(
    ['nombre' => 'Bodega Debug Prep'],
    ['tipo' => 'preparacion', 'direccion' => 'Debug']
);

// 2. Create Insumo
$insumo = Insumo::firstOrCreate(
    ['nombre' => 'Insumo Debug'],
    ['unidad_compra' => 'kg', 'unidad_consumo' => 'g', 'conversion' => 1000]
);

// 3. Clear pivot if exists
$bodega->insumos()->detach($insumo->id);

// 4. Attach with specific stock (e.g., 50)
$bodega->insumos()->attach($insumo->id, ['stock' => 50, 'costo_unitario_promedio' => 100]);

// 5. Update global stock (e.g., 500)
$insumo->stock = 500;
$insumo->save();

// 6. Test Query like Filament does
$result = $bodega->insumos()
    ->wherePivot('stock', '>', 0)
    ->where('insumos.id', $insumo->id)
    ->first();

echo "Bodega: " . $bodega->nombre . "\n";
echo "Insumo Global Stock: " . $insumo->stock . "\n";
echo "Relation Pivot Stock: " . ($result ? $result->pivot->stock : 'NULL') . "\n";

if ($result && $result->pivot->stock == 50) {
    echo "SUCCESS: Pivot stock correctly retrieved as 50.\n";
} else {
    echo "FAILURE: Pivot stock match failed.\n";
}

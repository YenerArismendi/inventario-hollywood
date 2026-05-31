<?php

namespace App\Filament\Actions;

use App\Models\Article;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Suppliers;
use App\Helpers\ProductHelper;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use OpenSpout\Reader\CSV\Options as CSVOptions;
use OpenSpout\Reader\CSV\Reader as CSVReader;
use OpenSpout\Reader\XLSX\Reader as XLSXReader;

class ImportArticlesAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'import_articles';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Importar Excel')
            ->icon('heroicon-o-arrow-up-tray')
            ->color('success')
            ->modalHeading('Importar Artículos desde Excel / CSV')
            ->modalWidth('2xl')
            ->form([
                Placeholder::make('instrucciones')
                    ->label('Instrucciones')
                    ->content(new HtmlString('
                        <div class="text-sm space-y-3">
                            <p>El archivo debe tener las siguientes columnas en la <strong>primera fila</strong> (encabezados):</p>
                            <div class="overflow-x-auto">
                                <table class="w-full text-xs border-collapse border border-gray-300 dark:border-gray-600">
                                    <thead class="bg-gray-100 dark:bg-gray-700">
                                        <tr>
                                            <th class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-left">Columna</th>
                                            <th class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-left">Obligatorio</th>
                                            <th class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-left">Ejemplo</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr><td class="border border-gray-300 dark:border-gray-600 px-2 py-1">nombre</td><td class="border px-2 py-1 text-green-600">✅ Sí</td><td class="border px-2 py-1">Shampoo Anticaspa</td></tr>
                                        <tr><td class="border border-gray-300 dark:border-gray-600 px-2 py-1">presentation</td><td class="border px-2 py-1 text-green-600">✅ Sí</td><td class="border px-2 py-1">400ml</td></tr>
                                        <tr><td class="border border-gray-300 dark:border-gray-600 px-2 py-1">unidad_medida</td><td class="border px-2 py-1 text-green-600">✅ Sí</td><td class="border px-2 py-1">unidad / kilo / litro / caja / mililitro</td></tr>
                                        <tr><td class="border border-gray-300 dark:border-gray-600 px-2 py-1">responsable</td><td class="border px-2 py-1 text-green-600">✅ Sí</td><td class="border px-2 py-1">María López</td></tr>
                                        <tr><td class="border border-gray-300 dark:border-gray-600 px-2 py-1">categoria</td><td class="border px-2 py-1 text-gray-400">❌ No</td><td class="border px-2 py-1">Cuidado Personal</td></tr>
                                        <tr><td class="border border-gray-300 dark:border-gray-600 px-2 py-1">marca</td><td class="border px-2 py-1 text-gray-400">❌ No</td><td class="border px-2 py-1">Head & Shoulders</td></tr>
                                        <tr><td class="border border-gray-300 dark:border-gray-600 px-2 py-1">proveedor</td><td class="border px-2 py-1 text-gray-400">❌ No</td><td class="border px-2 py-1">Distribuidora XYZ</td></tr>
                                        <tr><td class="border border-gray-300 dark:border-gray-600 px-2 py-1">precio_venta</td><td class="border px-2 py-1 text-gray-400">❌ No</td><td class="border px-2 py-1">15000 (default: 0)</td></tr>
                                        <tr><td class="border border-gray-300 dark:border-gray-600 px-2 py-1">costo</td><td class="border px-2 py-1 text-gray-400">❌ No</td><td class="border px-2 py-1">8000 (default: 0)</td></tr>
                                        <tr><td class="border border-gray-300 dark:border-gray-600 px-2 py-1">descripcion</td><td class="border px-2 py-1 text-gray-400">❌ No</td><td class="border px-2 py-1">Frasco azul 400ml</td></tr>
                                        <tr><td class="border border-gray-300 dark:border-gray-600 px-2 py-1">codigo_barras</td><td class="border px-2 py-1 text-gray-400">❌ No</td><td class="border px-2 py-1">7700000123456</td></tr>
                                        <tr><td class="border border-gray-300 dark:border-gray-600 px-2 py-1">is_preparacion</td><td class="border px-2 py-1 text-gray-400">❌ No</td><td class="border px-2 py-1">1 = preparado, 0 = no (default: 0)</td></tr>
                                        <tr><td class="border border-gray-300 dark:border-gray-600 px-2 py-1">stock</td><td class="border px-2 py-1 text-gray-400">❌ No</td><td class="border px-2 py-1">50 (default: 0)</td></tr>
                                        <tr><td class="border border-gray-300 dark:border-gray-600 px-2 py-1">bodega_id</td><td class="border px-2 py-1 text-gray-400">❌ No</td><td class="border px-2 py-1">1 (ID de la bodega)</td></tr>
                                    </tbody>
                                </table>
                            </div>
                            <a href="/templates/plantilla_articulos.csv" download
                               class="inline-flex items-center gap-1 text-primary-600 dark:text-primary-400 underline font-medium text-xs">
                                📥 Descargar plantilla CSV de ejemplo
                            </a>
                        </div>
                    ')),

                FileUpload::make('archivo')
                    ->label('Selecciona el archivo (.xlsx o .csv)')
                    ->acceptedFileTypes([
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'text/csv',
                        'application/csv',
                        'text/plain',
                    ])
                    ->disk('local')
                    ->directory('imports/temp')
                    ->required(),
            ])
            ->action(function (array $data): void {
                $path = Storage::disk('local')->path($data['archivo']);
                $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

                $headers = [];
                $rows = [];

                try {
                    if ($extension === 'xlsx') {
                        $reader = new XLSXReader();
                        $reader->open($path);
                        foreach ($reader->getSheetIterator() as $sheet) {
                            foreach ($sheet->getRowIterator() as $index => $row) {
                                $cells = array_map(
                                    fn ($cell) => trim((string) $cell->getValue()),
                                    $row->getCells()
                                );
                                if ($index === 1) {
                                    $headers = array_map('strtolower', $cells);
                                } else {
                                    if (! empty(array_filter($cells))) {
                                        $padded = array_slice(
                                            array_pad($cells, count($headers), ''),
                                            0, count($headers)
                                        );
                                        $rows[] = array_combine($headers, $padded);
                                    }
                                }
                            }
                            break; // solo primera hoja
                        }
                        $reader->close();
                    } else {
                        // CSV – intentar con ; y luego con ,
                        foreach ([';', ','] as $delimiter) {
                            $options = new CSVOptions();
                            $options->FIELD_DELIMITER = $delimiter;
                            $reader = new CSVReader($options);
                            $reader->open($path);
                            $tempHeaders = [];
                            $tempRows = [];
                            foreach ($reader->getSheetIterator() as $sheet) {
                                foreach ($sheet->getRowIterator() as $index => $row) {
                                    $cells = array_map(
                                        fn ($cell) => trim((string) $cell->getValue()),
                                        $row->getCells()
                                    );
                                    if ($index === 1) {
                                        $tempHeaders = array_map('strtolower', $cells);
                                    } else {
                                        if (! empty(array_filter($cells))) {
                                            $padded = array_slice(
                                                array_pad($cells, count($tempHeaders), ''),
                                                0, count($tempHeaders)
                                            );
                                            $tempRows[] = array_combine($tempHeaders, $padded);
                                        }
                                    }
                                }
                                break;
                            }
                            $reader->close();

                            if (count($tempHeaders) > 1) {
                                $headers = $tempHeaders;
                                $rows = $tempRows;
                                break;
                            }
                        }
                    }
                } catch (\Throwable $e) {
                    Notification::make()
                        ->title('Error al leer el archivo')
                        ->body($e->getMessage())
                        ->danger()
                        ->send();

                    return;
                }

                if (empty($headers)) {
                    Notification::make()
                        ->title('Archivo inválido')
                        ->body('No se pudieron leer los encabezados del archivo.')
                        ->danger()
                        ->send();

                    return;
                }

                $created = 0;
                $errors = [];

                foreach ($rows as $i => $row) {
                    $rowNum = $i + 2;

                    $nombre = $row['nombre'] ?? '';
                    $presentation = $row['presentation'] ?? '';
                    $unidadMedida = $row['unidad_medida'] ?? '';
                    $responsable = $row['responsable'] ?? '';

                    if (empty($nombre) || empty($presentation) || empty($unidadMedida) || empty($responsable)) {
                        $errors[] = "Fila {$rowNum}: faltan campos obligatorios (nombre, presentation, unidad_medida, responsable).";
                        continue;
                    }

                    // Categoría: crear si no existe
                    $categoryId = null;
                    if (! empty($row['categoria'])) {
                        $category = Category::firstOrCreate(['name' => $row['categoria']]);
                        $categoryId = $category->id;
                    }

                    // Marca: crear si no existe
                    $brandId = null;
                    if (! empty($row['marca'])) {
                        $brand = Brand::firstOrCreate(['name' => $row['marca']]);
                        $brandId = $brand->id;
                    }

                    // Proveedor: crear si no existe
                    $proveedorId = null;
                    if (! empty($row['proveedor'])) {
                        $proveedor = Suppliers::firstOrCreate(
                            ['name' => $row['proveedor']],
                            ['phone' => null, 'responsible' => null, 'email' => null, 'address' => null]
                        );
                        $proveedorId = $proveedor->id;
                    }

                    $isPreparacion = in_array(
                        strtolower(trim($row['is_preparacion'] ?? '0')),
                        ['1', 'si', 'sí', 'true', 'yes']
                    );

                    $precioVenta = (float) str_replace(['.', ','], ['', '.'], $row['precio_venta'] ?? '0');
                    $costo = (float) str_replace(['.', ','], ['', '.'], $row['costo'] ?? '0');

                    // Generar código único usando el helper
                    $brandName = $brandId ? Brand::find($brandId)?->name : 'GEN';
                    $codigo = ProductHelper::generarCodigoUnico($nombre, $brandName ?? 'GEN', $presentation);

                    try {
                        $match = [];
                        $codigoBarras = !empty($row['codigo_barras']) ? $row['codigo_barras'] : null;
                        
                        if ($codigoBarras) {
                            $match['codigo_barras'] = $codigoBarras;
                        } else {
                            $match['nombre'] = $nombre;
                            $match['presentation'] = $presentation;
                        }

                        $articleData = [
                            'nombre'         => $nombre,
                            'presentation'   => $presentation,
                            'unidad_medida'  => $unidadMedida,
                            'responsable'    => $responsable,
                            'category_id'    => $categoryId,
                            'brand_id'       => $brandId,
                            'proveedor_id'   => $proveedorId,
                            'precio_venta'   => $precioVenta,
                            'costo'          => $costo,
                            'descripcion'    => $row['descripcion'] ?? null,
                            'codigo_barras'  => $codigoBarras,
                            'is_preparacion' => $isPreparacion,
                            'estado'         => true,
                        ];

                        $article = Article::withTrashed()->where($match)->first();
                        
                        if ($article) {
                            if ($article->trashed()) {
                                $article->restore();
                            }
                            // Si existe y no tiene código interno, se lo generamos
                            if (empty($article->codigo)) {
                                $articleData['codigo'] = $codigo;
                            }
                            $article->update($articleData);
                        } else {
                            $articleData['codigo'] = $codigo;
                            $article = Article::create($articleData);
                        }

                        // Asignar a bodega con stock
                        $stock = (float) str_replace(',', '.', $row['stock'] ?? '0');
                        $bodegaId = trim($row['bodega_id'] ?? '');
                        
                        if ($stock > 0 && !empty($bodegaId)) {
                            // Buscar la bodega por ID
                            $bodega = \App\Models\Bodega::find($bodegaId);
                            
                            if (!$bodega) {
                                throw new \Exception("La bodega con ID {$bodegaId} no existe.");
                            }
                            
                            // Asociar el artículo a la bodega con el stock inicial sin duplicar registros
                            $article->bodegas()->syncWithoutDetaching([
                                $bodega->id => ['stock' => $stock]
                            ]);
                        }

                        $created++;
                    } catch (\Throwable $e) {
                        $errors[] = "Fila {$rowNum} ({$nombre}): " . $e->getMessage();
                    }
                }

                // Limpiar archivo temporal
                Storage::disk('local')->delete($data['archivo']);

                if ($created > 0) {
                    $body = count($errors) > 0
                        ? 'Con errores en: ' . implode(', ', array_slice($errors, 0, 3)) . (count($errors) > 3 ? '...' : '')
                        : null;

                    Notification::make()
                        ->title("✅ {$created} artículo(s) importado(s)")
                        ->body($body)
                        ->success()
                        ->send();
                } else {
                    Notification::make()
                        ->title('No se importó ningún artículo')
                        ->body(implode("\n", array_slice($errors, 0, 5)))
                        ->danger()
                        ->send();
                }
            });
    }
}

<?php

namespace App\Filament\Actions;

use App\Models\Insumo;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use OpenSpout\Reader\CSV\Options as CSVOptions;
use OpenSpout\Reader\CSV\Reader as CSVReader;
use OpenSpout\Reader\XLSX\Reader as XLSXReader;

class ImportInsumoAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'import_insumos';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Importar Excel')
            ->icon('heroicon-o-arrow-up-tray')
            ->color('success')
            ->modalHeading('Importar Insumos desde Excel / CSV')
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
                                            <th class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-left">Valores aceptados / Ejemplo</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr><td class="border border-gray-300 dark:border-gray-600 px-2 py-1">nombre</td><td class="border px-2 py-1 text-green-600">✅ Sí</td><td class="border px-2 py-1">Aceite de Coco</td></tr>
                                        <tr><td class="border border-gray-300 dark:border-gray-600 px-2 py-1">unidad_compra</td><td class="border px-2 py-1 text-green-600">✅ Sí</td><td class="border px-2 py-1">litro / paquete / galones</td></tr>
                                        <tr><td class="border border-gray-300 dark:border-gray-600 px-2 py-1">unidad_consumo</td><td class="border px-2 py-1 text-green-600">✅ Sí</td><td class="border px-2 py-1">mililitros / unidad / litros</td></tr>
                                        <tr><td class="border border-gray-300 dark:border-gray-600 px-2 py-1">conversion</td><td class="border px-2 py-1 text-green-600">✅ Sí</td><td class="border px-2 py-1">1000 (1 litro = 1000 ml)</td></tr>
                                        <tr><td class="border border-gray-300 dark:border-gray-600 px-2 py-1">stock</td><td class="border px-2 py-1 text-gray-400">❌ No</td><td class="border px-2 py-1">100 (default: 0)</td></tr>
                                        <tr><td class="border border-gray-300 dark:border-gray-600 px-2 py-1">bodega_id</td><td class="border px-2 py-1 text-gray-400">❌ No</td><td class="border px-2 py-1">1 (ID de la bodega)</td></tr>
                                    </tbody>
                                </table>
                            </div>
                            <p class="text-gray-500 text-xs">El stock y costo se inician en <strong>0</strong> y se actualizan al registrar compras.</p>
                            <a href="/templates/plantilla_insumos.csv" download
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
                            break;
                        }
                        $reader->close();
                    } else {
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
                    $unidadCompra = $row['unidad_compra'] ?? '';
                    $unidadConsumo = $row['unidad_consumo'] ?? '';
                    $conversion = $row['conversion'] ?? '';

                    if (empty($nombre) || empty($unidadCompra) || empty($unidadConsumo) || empty($conversion)) {
                        $errors[] = "Fila {$rowNum}: faltan campos obligatorios (nombre, unidad_compra, unidad_consumo, conversion).";
                        continue;
                    }

                    try {
                        $insumo = Insumo::create([
                            'nombre'                 => $nombre,
                            'unidad_compra'          => $unidadCompra,
                            'unidad_consumo'         => $unidadConsumo,
                            'conversion'             => (float) str_replace(',', '.', $conversion),
                            'stock'                  => 0,
                            'costo_unitario_promedio' => 0,
                        ]);

                        // Asignar a bodega con stock
                        $stock = (float) str_replace(',', '.', $row['stock'] ?? '0');
                        $bodegaId = trim($row['bodega_id'] ?? '');
                        
                        if ($stock > 0 && !empty($bodegaId)) {
                            // Buscar la bodega por ID
                            $bodega = \App\Models\Bodega::find($bodegaId);
                            
                            if (!$bodega) {
                                throw new \Exception("La bodega con ID {$bodegaId} no existe.");
                            }
                            
                            // Asociar el insumo a la bodega con el stock inicial sin duplicar registros
                            $insumo->bodegas()->syncWithoutDetaching([
                                $bodega->id => [
                                    'stock' => $stock,
                                    'costo_unitario_promedio' => 0
                                ]
                            ]);
                            
                            // Si se asigna stock a una bodega, también actualizamos el stock global del insumo
                            $insumo->update(['stock' => $stock]);
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
                        ->title("✅ {$created} insumo(s) importado(s)")
                        ->body($body)
                        ->success()
                        ->send();
                } else {
                    Notification::make()
                        ->title('No se importó ningún insumo')
                        ->body(implode("\n", array_slice($errors, 0, 5)))
                        ->danger()
                        ->send();
                }
            });
    }
}

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InsumoResource\Pages;
use App\Models\Insumo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InsumoResource extends Resource
{
    protected static ?string $model = Insumo::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    public static function getNavigationGroup(): ?string
    {
        return 'Preparaciones';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->label('Nombre')
                    ->required(),
                Forms\Components\Select::make('unidad_compra')
                    ->label('Unidad de Compra')
                    ->options([
                        'litro' => 'Litro',
                        'paquete' => 'Paquete',
                        'galones' => 'Galones',
                    ])
                    ->required(),
                Forms\Components\Select::make('unidad_consumo')
                    ->label('Unidad de Consumo')
                    ->options([
                        'mililitros' => 'Mililitros',
                        'unidad' => 'Unidad',
                        'litros' => 'litros',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('conversion')
                    ->label('Rango de conversion')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Nombre'),
                Tables\Columns\TextColumn::make('unidad_compra')
                    ->label('Unidad de Compra'),
                Tables\Columns\TextColumn::make('unidad_consumo')
                    ->label('Unidad de Consumo'),
                Tables\Columns\TextColumn::make('conversion')
                    ->label('Rango de conversión')
                    ->formatStateUsing(function ($state, $record) {
                        // Ejemplo: 1 paquete = 500 unidades
                        return "1 {$record->unidad_compra} = {$state} {$record->unidad_consumo}";
                    }),
                Tables\Columns\TextColumn::make('stock')
                    ->label('Stock Actual')
                    ->sortable()
                    ->formatStateUsing(function ($state, Insumo $record) {
                        $user = auth()->user();
                        $bodegaActivaId = $user->active_bodega_id;

                        // Si hay bodega activa seleccionada, mostrar stock de esa bodega ESPECÍFICA
                        if ($bodegaActivaId) {
                            $stockEnBodega = $record->bodegas()
                                ->where('bodegas.id', $bodegaActivaId)
                                ->value('bodega_insumo.stock') ?? 0;

                            return '<div class="font-bold">' . number_format($stockEnBodega, 0, ',', '.') . ' ' . $record->unidad_consumo . '</div>';
                        }

                        // Lógica para Super Admin Global (Sin bodega seleccionada): Mostrar detalle completo
                        if ($user->hasRole('super_admin')) {
                            $html = '<div class="space-y-1">';
                            // Mostrar Total Global explícitamente
                            $html .= '<div class="font-bold text-gray-800 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-1 mb-1">Glob: ' . number_format($record->stock, 0, ',', '.') . ' ' . $record->unidad_consumo . '</div>';

                            if ($record->bodegas->isNotEmpty()) {
                                $html .= '<div class="text-xs text-gray-500 dark:text-gray-400 pl-1 space-y-0.5">';
                                foreach ($record->bodegas as $bodega) {
                                    if ($bodega->pivot->stock > 0) {
                                        $html .= '<div class="flex justify-between items-center"><span class="font-medium mr-2">' . $bodega->nombre . ':</span> <span>' . number_format($bodega->pivot->stock, 0, ',', '.') . '</span></div>';
                                    }
                                }
                                $html .= '</div>';
                            } else {
                                $html .= '<div class="text-xs italic text-gray-400">Sin ubicación asignada</div>';
                            }
                            $html .= '</div>';
                            return $html;
                        }

                        // Lógica fallback para Trabajadores sin bodega activa (muestra suma de sus asignadas)
                        $stockAsignado = $record->bodegas()
                            ->whereIn('bodegas.id', $user->bodegas->pluck('id'))
                            ->sum('bodega_insumo.stock');

                        return '<div class="font-bold">' . number_format($stockAsignado, 0, ',', '.') . ' ' . $record->unidad_consumo . '</div>';
                    })
                    ->html()
                    ->color(fn(Insumo $record) => $record->stock < 10 ? 'danger' : 'success'),
                Tables\Columns\TextColumn::make('costo_unitario_promedio')
                    ->label('Costo Promedio')
                    ->formatStateUsing(fn($state) => '$' . number_format($state, 2, ',', '.'))
                    ->color('warning'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('ver_stock')
                    ->label('Ver Stock')
                    ->icon('heroicon-o-eye')
                    ->visible(fn() => auth()->user()->hasRole('super_admin'))
                    ->modalHeading(fn(Insumo $record) => "Stock de " . $record->nombre)
                    ->modalContent(function (Insumo $record) {
                        return view('filament.resources.insumo-resource.pages.ver-stock', ['record' => $record]);
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelAction(fn($action) => $action->label('Cerrar')),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->modalHeading('Eliminar Insumo')
                    ->modalDescription('¿Estás seguro de que deseas eliminar este insumo? Se borrará su historial de compras y afectará las recetas donde se utiliza.'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->modalHeading('Eliminar Insumos Seleccionados')
                        ->modalDescription('¿Estás seguro de que deseas eliminar estos insumos? Se borrará el historial de compras de todos ellos y afectará las recetas donde se utilizan.'),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();
        $query = parent::getEloquentQuery();

        // Si el usuario tiene una bodega activa, filtramos los insumos que están en esa bodega (opcional)
        // O simplemente nos aseguramos de que el cálculo de stock se base en esa bodega.
        // El requerimiento dice "actualicen con los datos referentes a cada bodega".
        // Si filtramos por la bodega, solo veremos los insumos QUe TIENEN relación con esa bodega.

        if ($user && $user->active_bodega_id) {
            // Opcional: Mostrar solo insumos que tienen stock o registro en esa bodega
            // $query->whereHas('bodegas', function ($q) use ($user) {
            //    $q->where('bodega_insumo.bodega_id', $user->active_bodega_id);
            // });

            // Por ahora, para ser consistentes con ArticleResource, haremos el filtro:
            $query->whereHas('bodegas', function (Builder $q) use ($user) {
                $q->where('bodega_insumo.bodega_id', $user->active_bodega_id);
            });
        } elseif ($user && !$user->hasRole('super_admin')) {
            // Fallback para trabajadores sin bodega activa (aunque deberían tenerla)
            // Mostrar nada o dejar el query por defecto?
            // Dejamos por defecto pero el stock calculation se encargará.
        }

        return $query;
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Resources\InsumoResource\RelationManagers\BodegasRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInsumos::route('/'),
            'create' => Pages\CreateInsumo::route('/create'),
            'edit' => Pages\EditInsumo::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Resources;

use App\Filament\Actions\ImportInsumoAction;
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
                Tables\Columns\TextColumn::make('bodegas_sum_stock')
                    ->label('Stock Total')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(function ($state, Insumo $record) {
                        return number_format($state ?? 0, 0, ',', '.') . ' ' . $record->unidad_consumo;
                    })
                    ->color(fn(Insumo $record) => $record->bodegas_sum_stock < 10 ? 'danger' : 'success'),
                Tables\Columns\TextColumn::make('costo_unitario_promedio')
                    ->label('Costo Promedio')
                    ->formatStateUsing(fn($state) => '$' . number_format($state, 2, ',', '.'))
                    ->color('warning'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado'),
            ])
            ->headerActions([
                ImportInsumoAction::make(),
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
        
        $stockSubquery = 'select sum(stock) from bodega_insumo where insumo_id = insumos.id';
        if ($user && $user->active_bodega_id && $user->active_bodega_id != 1) {
            $stockSubquery .= " and bodega_id = {$user->active_bodega_id}";
        }

        $query = parent::getEloquentQuery()
            ->select('insumos.*')
            ->selectSub(
                $stockSubquery,
                'bodegas_sum_stock'
            );

        if (!$user || $user->hasRole('admin') || $user->hasRole('super_admin')) {
            return $query;
        }

        if ($user->active_bodega_id) {
            $query->whereHas('bodegas', function (Builder $q) use ($user) {
                $q->where('bodega_insumo.bodega_id', $user->active_bodega_id);
            });
        } else {
            $query->whereRaw('0 = 1');
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

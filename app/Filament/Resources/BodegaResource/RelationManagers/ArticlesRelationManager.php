<?php

namespace App\Filament\Resources\BodegaResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ArticlesRelationManager extends RelationManager
{
    protected static string $relationship = 'articles';
    protected static ?string $recordTitleAttribute = 'nombre';

    public function form(Form $form): Form
    {
        // Este formulario se usa para la acción de "Editar" un artículo ya adjunto.
        // Debe contener todos los campos de la tabla pivote que quieras editar.
        return $form
            ->schema([
                Forms\Components\TextInput::make('stock')
                    ->label('Cantidad en Stock')
                    ->numeric()
                    ->required()
                    ->readOnly() // Stock solo lectura
                    ->helperText('El stock solo se puede modificar mediante transferencias o ajustes de inventario.'),

                // Selector para la columna del estante
                Forms\Components\Select::make('columna')
                    ->label('Columna')
                    ->options(array_combine(range('A', 'Z'), range('A', 'Z')))
                    ->searchable(),

                // Selector para la fila del estante
                Forms\Components\Select::make('fila')
                    ->label('Fila')
                    ->options(array_combine(range(1, 20), range(1, 20)))
                    ->searchable(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            /* ->modifyQueryUsing(function (Builder $query) {
                return $query->where('bodega_article.stock', '>', 0);
            }) */
            ->columns([
                Tables\Columns\TextColumn::make('nombre'),
                Tables\Columns\TextColumn::make('codigo')->label('Codigo'),
                // Esta columna especial muestra el dato de la tabla pivote
                Tables\Columns\TextColumn::make('pivot.stock')
                    ->label('Stock en esta Bodega')
                    ->badge()
                    ->color(fn(int $state): string => match (true) {
                        $state === 0 => 'danger',
                        $state < 10 => 'warning',
                        default => 'success',
                    })
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('bodega_article.stock', $direction);
                    }),
                Tables\Columns\TextColumn::make('pivot.columna')
                    ->label('Columna')
                    ->sortable(),
                Tables\Columns\TextColumn::make('pivot.fila')
                    ->label('Fila')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Acción para "adjuntar" un artículo del catalogo al inventario de esta bodega
                Tables\Actions\AttachAction::make()
                    ->label('Añadir Artículo al Inventario')
                    ->modalHeading('Vincular Artículo')
                    ->form(fn(Tables\Actions\AttachAction $action): array => [
                        // Le decimos explícitamente dónde buscar.
                        $action->getRecordSelect()
                            ->label('Artículo del Catálogo')
                            ->searchable(['nombre', 'codigo'])
                            ->getOptionLabelFromRecordUsing(fn($record) => "{$record->nombre} (Cód: {$record->codigo})")
                            ->placeholder('Seleccionar Articulo')
                            ->preload(50)
                            ->required(),
                        Forms\Components\Hidden::make('stock')
                            ->default(0), // Por defecto 0 al vincular

                        // Añadimos los campos de ubicación también aquí
                        Forms\Components\Select::make('columna')
                            ->label('Columna')
                            ->placeholder('Seleccionar columna')
                            ->options(array_combine(range('A', 'Z'), range('A', 'Z')))
                            ->searchable(),
                        Forms\Components\Select::make('fila')
                            ->label('Fila')
                            ->placeholder('Seleccionar fila')
                            ->options(array_combine(range(1, 20), range(1, 20)))
                            ->searchable(),
                    ]),
            ])
            ->actions([
                // Acción para editar el stock de un artículo ya en el inventario
                Tables\Actions\EditAction::make()
                    ->label('Editar'),
                Tables\Actions\DetachAction::make()
                    ->label('Quitar del Inventario'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make()->label('Quitar seleccionados'),
                ]),
            ]);
    }
}

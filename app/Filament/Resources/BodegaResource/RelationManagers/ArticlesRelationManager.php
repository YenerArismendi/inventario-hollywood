<?php

namespace App\Filament\Resources\BodegaResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ArticlesRelationManager extends RelationManager
{
    protected static string $relationship = 'articles';
    protected static ?string $recordTitleAttribute = 'nombre';

    public function form(Form $form): Form
    {
        // Este formulario se usa para la acción de "Editar" un artículo ya adjunto.
        // Solo debe contener campos de la tabla pivote, como el stock.
        return $form
            ->schema([
                Forms\Components\TextInput::make('stock')
                    ->label('Cantidad en Stock')
                    ->numeric() // Usamos numeric() para el campo de stock
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre'),
                Tables\Columns\TextColumn::make('codigo')->label('Codigo'),
                // Esta columna especial muestra el dato de la tabla pivote
                Tables\Columns\TextColumn::make('stock')
                    ->label('Stock en esta Bodega')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Acción para "adjuntar" un artículo del catalogo al inventario de esta bodega
                Tables\Actions\AttachAction::make()
                    ->label('Añadir Artículo al Inventario')
                    ->form(fn(Tables\Actions\AttachAction $action): array => [
                        // Le decimos explícitamente dónde buscar.
                        $action->getRecordSelect()
                            ->label('Artículo del Catálogo')
                            ->searchable(['nombre', 'codigo'])
                            ->preload(),
                        Forms\Components\TextInput::make('stock')->numeric()->required()->default(1),
                    ]),
            ])
            ->actions([
                // Acción para editar el stock de un artículo ya en el inventario
                Tables\Actions\EditAction::make()
                    ->label('Editar Stock'),
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

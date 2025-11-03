<?php

namespace App\Filament\Resources\BodegaResource\RelationManagers;

use App\Models\Estante;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;

class EstantesRelationManager extends RelationManager
{
    protected static string $relationship = 'estantes'; // Relación definida en el modelo Bodega

    public function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('nivel')
                    ->numeric()
                    ->default(1)
                    ->minValue(1)
                    ->required(),
                Forms\Components\TextInput::make('filas')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('columnas')
                    ->numeric()
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')->label('Nombre'),
                Tables\Columns\TextColumn::make('filas')->label('Filas'),
                Tables\Columns\TextColumn::make('columnas')->label('Columnas'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Action::make('verMapa')
                    ->label('Ver Mapa')
                    ->icon('heroicon-o-eye')
                    ->modalHeading(fn($record) => 'Mapa del estante: ' . $record->nombre)
                    ->modalContent(fn($record) => view('filament.modals.mapa-estante', [
                        'estante' => $record->load('ubicaciones.producto'),
                    ]))
                    ->modalWidth('7xl'),
                // 🔹 Acción para editar
                Tables\Actions\EditAction::make(),

                // 🔹 Acción para eliminar
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}

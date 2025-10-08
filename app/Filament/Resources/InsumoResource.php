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
                    ->label('Stock'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
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

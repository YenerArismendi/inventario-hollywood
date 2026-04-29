<?php

namespace App\Filament\Resources\CompraProductoResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\Summarizers\Sum;

class CompraDetallesRelationManager extends RelationManager
{
    protected static string $relationship = 'compraDetalles';

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('article.nombre')
                    ->label('Producto'),

                TextColumn::make('cantidad')
                    ->label('Cantidad'),

                TextColumn::make('costo_unitario')
                    ->label('Costo unitario')
                    ->money('COP'),

                TextColumn::make('costo_total')
                    ->label('Costo total')
                    ->money('COP')
                    ->summarize(
                        Sum::make()
                            ->label('Total Compra')
                            ->money('COP')
                    ),
            ]);
    }
}

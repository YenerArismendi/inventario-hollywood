<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VentaResource\Pages;
use App\Models\Venta;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Navigation\NavigationItem;

class VentaResource extends Resource
{
    protected static ?string $model = Venta::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Ventas';
    protected static ?string $label = 'Historial de Venta';
    protected static ?string $pluralLabel = 'Historial de Ventas';


    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID Venta')->sortable(),
                Tables\Columns\TextColumn::make('cliente.nombre')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('total')->money('cop')->sortable(),
                Tables\Columns\TextColumn::make('metodo_pago')->badge(),
                Tables\Columns\TextColumn::make('user.name')->label('Vendedor')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label('Fecha')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVentas::route('/'),
            'view' => Pages\ViewVenta::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        // Se deshabilita la creación de ventas desde el panel administrativo.
        return false;
    }
}

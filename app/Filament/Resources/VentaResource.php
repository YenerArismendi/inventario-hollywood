<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VentaResource\Pages;
use App\Models\Venta;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Navigation\NavigationItem;

class VentaResource extends Resource
{
    protected static ?string $model = Venta::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Ventas';


    public static function table(Table $table): Table
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
            'create' => Pages\PointOfSale::route('/create'),
            'view' => Pages\ViewVenta::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false; // Ocultamos el botón "New Venta" de la tabla para una experiencia más limpia
    }

    public static function getNavigationItems(): array
    {
        return [
            NavigationItem::make('Punto de Venta')
                ->url(static::getUrl('create')) // Apunta a nuestra página PointOfSale
                ->icon('heroicon-o-shopping-bag') // Un icono diferente para distinguirlo
                ->group(static::getNavigationGroup())
                ->sort(1), // Lo ponemos primero en el grupo "Ventas"

            NavigationItem::make('Historial de Ventas')
                ->url(static::getUrl('index')) // Apunta a la lista de ventas
                ->icon(static::getNavigationIcon()) // El icono original del recurso
                ->group(static::getNavigationGroup())
                ->isActiveWhen(fn() => request()->routeIs(static::getRouteBaseName() . '.index'))
                ->sort(2), // Lo ponemos segundo
        ];
    }
}

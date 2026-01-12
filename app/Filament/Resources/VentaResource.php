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
                Tables\Columns\TextColumn::make('tipo_venta')
                    ->label('Tipo de Venta')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'presencial' => 'success',
                        'virtual' => 'info',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')->label('Vendedor')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label('Fecha')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->modalHeading('Eliminar Venta')
                    ->modalDescription('¿Estás seguro de que deseas eliminar esta venta? Esto afectará los reportes financieros y el historial de ventas.'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->modalHeading('Eliminar Ventas Seleccionadas')
                    ->modalDescription('¿Estás seguro de que deseas eliminar las ventas seleccionadas? Esto afectará los reportes financieros y el historial de ventas.'),
            ]);
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

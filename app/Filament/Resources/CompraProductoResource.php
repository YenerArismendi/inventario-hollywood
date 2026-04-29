<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompraProductoResource\Pages;
use App\Filament\Resources\CompraProductoResource\RelationManagers\CompraDetallesRelationManager;
use App\Models\Compra;
use App\Models\Article;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Support\RawJs;

class CompraProductoResource extends Resource
{
    protected static ?string $model = Compra::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationLabel = 'Compras Productos';
    protected static ?string $pluralLabel = 'Compras Productos';
    protected static ?string $label = 'Compra Producto';
    protected static ?string $navigationGroup = null;

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Select::make('proveedor_id')
                    ->relationship('proveedor', 'name')
                    ->label('Proveedor')
                    ->required(),

                Select::make('bodega_id')
                    ->relationship('bodega', 'nombre')
                    ->label('Bodega')
                    ->required(),

                Repeater::make('articulos')
                    ->label('Artículos')
                    ->schema([
                        Select::make('article_id')
                            ->label('Producto')
                            ->placeholder('Selecciona producto')
                            ->options(fn () => Article::orderBy('nombre')->pluck('nombre', 'id'))
                            ->searchable()
                            ->required(),

                        TextInput::make('cantidad')
                            ->numeric()
                            ->minValue(1)
                            ->label('Cantidad')
                            ->placeholder('Cantidad')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $costoUnitarioRaw = $get('costo_unitario') ?? '0';
                                $costoUnitarioLimpio = (float) str_replace('.', '', $costoUnitarioRaw);
                                $cantidadLimpia = (float) str_replace('.', '', $state ?: '0');
                                $set('costo_total', $cantidadLimpia * $costoUnitarioLimpio);
                            }),

                        TextInput::make('costo_unitario')
                            ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
                            ->stripCharacters('.')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('$')
                            ->label('Costo unitario')
                            ->placeholder('Ej: 1.500')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $cantidadRaw = $get('cantidad') ?? '0';
                                $cantidadLimpia = (float) str_replace('.', '', $cantidadRaw);
                                $costoUnitarioRaw = $state ?: '0';
                                $costoUnitarioLimpio = (float) str_replace('.', '', $costoUnitarioRaw);
                                $set('costo_total', $cantidadLimpia * $costoUnitarioLimpio);
                            }),

                        TextInput::make('costo_total')
                            ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
                            ->stripCharacters('.')
                            ->numeric()
                            ->prefix('$')
                            ->label('Costo total')
                            ->placeholder('Costo total')
                            ->disabled()
                            ->dehydrated(),
                    ])
                    // 4 columnas para que Producto, Cantidad, C. Unitario y C. Total queden en línea
                    ->columns(['default' => 1, 'md' => 4])
                    ->columnSpanFull()
                    ->addActionLabel('Agregar producto')
                    ->required(),
            ])
            ->columns(['default' => 1, 'sm' => 2]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('proveedor.name')->label('Proveedor'),
                TextColumn::make('bodega.nombre')->label('Bodega'),
                TextColumn::make('fecha')->date(),
                TextColumn::make('total')
                    ->label('Total')
                    ->numeric(
                        decimalPlaces: 0,
                        decimalSeparator: ',',
                        thousandsSeparator: '.'
                    )
                    ->prefix('$ ')
                    ->suffix(' COP')
                    ->color('success'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCompraProductos::route('/'),
            'create' => Pages\CreateCompraProducto::route('/create'),
            'edit' => Pages\EditCompraProducto::route('/{record}/edit'),
            'view' => Pages\ViewCompraProducto::route('/{record}'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            CompraDetallesRelationManager::class,
        ];
    }
}

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompraResource\Pages;
use App\Models\Compra;
use App\Models\Insumo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;

use Filament\Forms\Get;
use Filament\Forms\Set;

class CompraResource extends Resource
{
    protected static ?string $model = Compra::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    public static function getNavigationGroup(): ?string
    {
        return 'Preparaciones';
    }

    protected static ?string $label = 'Compra';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('proveedor_id')
                    ->relationship('proveedor', 'name')
                    ->label('Proveedor')
                    ->required()
                    ->searchable()
                    ->preload(),

                DatePicker::make('fecha')
                    ->label('Fecha de compra')
                    ->required(),

                Repeater::make('detalles')
                    ->relationship('detalles')
                    ->label('Detalle de insumos')
                    ->schema([
                        Select::make('insumo_id')
                            ->label('Insumo')
                            ->relationship('insumo', 'nombre')
                            ->reactive()
                            ->preload()
                            ->searchable()
                            // opcional: precargar un precio si tu modelo Insumo lo trae
                            ->afterStateUpdated(function (Get $get, Set $set, $state = null) {
                                if (!$state) return;
                                $insumo = \App\Models\Insumo::find($state);
                                if ($insumo) {
                                    // si guardas un último precio o quieres precargar, cámbialo por tu campo real
                                    $set('precio_unitario', $insumo->ultimo_precio_compra ?? 0);
                                    // y recalculamos subtotal si hay cantidad
                                    $set('subtotal', ($get('cantidad') ?? 0) * ($insumo->ultimo_precio_compra ?? 0));
                                }
                            }),

                        TextInput::make('cantidad')
                            ->label('Cantidad (unidad de compra)')
                            ->numeric()
                            ->reactive()
                            ->required()
                            ->afterStateUpdated(function (Get $get, Set $set, $state = null) {
                                $cantidad = $get('cantidad') ?? 0;
                                $precio = $get('precio_unitario') ?? 0;
                                $set('subtotal', $cantidad * $precio);
                            }),

                        TextInput::make('costo_unitario')
                            ->label('Precio unitario (por unidad de compra)')
                            ->numeric()
                            ->reactive()
                            ->required()
//                            ->prefix('COP $')
                            ->afterStateUpdated(function (Get $get, Set $set, $state = null) {
                                $cantidad = $get('cantidad') ?? 0;
                                $precio = $get('costo_unitario') ?? 0;
                                $set('costo_total', $cantidad * $precio);
                            }),

                        // Subtotal del item (se guarda si tu modelo tiene la columna 'subtotal')
                        TextInput::make('costo_total')
                            ->label('Subtotal')
                            ->disabled()
                            ->dehydrated(true), // si quieres que se persista en DB, deja true; si no, false
//                            ->prefix('COP $'),
                    ])
                    ->columns(4)
                    // Cuando el repeater cambie (agregar/quitar item o actualizar), recalculamos el total general
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        $detalles = $get('detalles') ?? [];
                        $total = collect($detalles)->sum(fn($item) => $item['costo_total'] ?? 0);
                        $set('total', $total);
                    })
                    ->createItemButtonLabel('Agregar Insumo'),

                TextInput::make('total')
                    ->label('Total compra')
//                    ->prefix('COP $')
                    ->disabled()
                    ->dehydrated(true) // querer persistir el total en compras.total
                    ->default(function (Get $get) {
                        $detalles = $get('detalles') ?? [];
                        return collect($detalles)->sum(fn($item) => $item['costo_total'] ?? 0);
                    })
                    ->reactive(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('proveedor.name')->label('Proveedor')->sortable(),
                Tables\Columns\TextColumn::make('detalles.insumo.nombre')
                    ->label('Insumo')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('fecha')->label('Fecha'),
                Tables\Columns\TextColumn::make('total')
                    ->label('Total (COP)')
                    ->formatStateUsing(fn($state) => '$ ' . number_format($state, 0, ',', '.')),
                Tables\Columns\TextColumn::make('created_at')->label('Creado'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCompras::route('/'),
            'create' => Pages\CreateCompra::route('/create'),
            'edit' => Pages\EditCompra::route('/{record}/edit'),
        ];
    }
}


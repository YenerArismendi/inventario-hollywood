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
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\ViewField;

class CompraResource extends Resource
{
    protected static ?string $model = Compra::class;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $label = 'Compra';

    public static function getNavigationGroup(): ?string
    {
        return 'Preparaciones';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Detalles de Insumos')
                    ->schema([
                        ViewField::make('tabla-insumos')
                            ->view('filament.compra.tabla-insumos')
                            ->viewData([
                                'insumos' => \App\Models\Insumo::all(),
                            ])
                            ->afterStateHydrated(function ($component, $state, $record) {
                                if (!$record) {
                                    $component->state([
                                        'detallesJson' => '[]',
                                    ]);
                                    return;
                                }

                                $detalles = $record->detalles()->with('insumo')->get()->map(function ($detalle) {
                                    return [
                                        'insumo_id' => $detalle->insumo_id,
                                        'nombre' => $detalle->insumo->nombre ?? '',
                                        'cantidad' => $detalle->cantidad,
                                        'costo_unitario' => $detalle->costo_unitario,
                                        'costo_total' => $detalle->costo_total,
                                    ];
                                });

                                $component->state([
                                    'detallesJson' => json_encode($detalles, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP),
                                ]);
                            }),
                    ]),

            ])
            ->extraAttributes(['id' => 'compra-form']);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('proveedor.name')->label('Proveedor')->sortable(),
                Tables\Columns\TextColumn::make('fecha')->label('Fecha'),
                Tables\Columns\TextColumn::make('total')
                    ->label('Total (COP)')
                    ->formatStateUsing(fn($state) => '$ ' . number_format($state, 0, ',', '.')),
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

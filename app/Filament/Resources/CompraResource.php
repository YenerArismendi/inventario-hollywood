<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompraResource\Pages;
use App\Models\Compra;
use App\Models\Insumo;
use App\Models\Suppliers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Tables;
use Filament\Resources\Resource;
use Filament\Tables\Table;

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
                Section::make('Detalles de la compra')
                    ->schema([

                        // Campo oculto para los detalles JSON
                        Hidden::make('detalles_json')
                            ->default('[]'),

                        // Tu tabla de insumos como ViewField
                        ViewField::make('tabla_insumos')
                            ->view('filament.compra.tabla-insumos')
                            ->viewData(function ($record) {
                                $insumos = Insumo::select('id', 'nombre')->get();

                                $detalles = $record
                                    ? $record->detalles()->with('insumo')->get()->map(function ($d) {
                                        return [
                                            'insumo_id' => $d->insumo_id,
                                            'nombre' => $d->insumo->nombre ?? '',
                                            'cantidad' => $d->cantidad,
                                            'costo_unitario' => $d->costo_unitario,
                                            'costo_total' => $d->costo_total,
                                        ];
                                    })->toArray()
                                    : [];

                                return [
                                    'insumosJson' => json_encode($insumos, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP),
                                    'detallesJson' => json_encode($detalles, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP),
                                    'compra' => $record,
                                    'compraId' => $record?->id,
                                ];
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

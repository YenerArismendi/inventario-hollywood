<?php

namespace App\Filament\Resources\ClienteResource\RelationManagers;

use App\Models\Venta;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class VentasRelationManager extends RelationManager
{
    protected static string $relationship = 'ventas';

    protected static ?string $title = 'Historial de Compras';

    protected static ?string $modelLabel = 'Venta';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // No habilitamos creación desde aquí por ahora, ya que las ventas se hacen desde el POS
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->money('cop')
                    ->sortable(),

                Tables\Columns\TextColumn::make('metodo_pago')
                    ->label('Método')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'efectivo' => 'success',
                        'transferencia' => 'info',
                        'credito' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('estado')
                    ->label('Estado')
                    ->badge(),

                Tables\Columns\TextColumn::make('vencimiento')
                    ->label('Plazo / Vencimiento')
                    ->getStateUsing(function (Venta $record) {
                        if ($record->metodo_pago !== 'credito') {
                            return 'N/A';
                        }

                        $diasCredito = $record->cliente->dias_credito ?? 0;
                        $fechaVencimiento = $record->created_at->addDays($diasCredito);

                        return $fechaVencimiento->diffForHumans();
                    })
                    ->color(function (Venta $record) {
                        if ($record->metodo_pago !== 'credito') return 'gray';

                        $diasCredito = $record->cliente->dias_credito ?? 0;
                        $fechaVencimiento = $record->created_at->addDays($diasCredito);

                        return $fechaVencimiento->isPast() ? 'danger' : 'success';
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('ver_detalles')
                    ->label('Ver Productos')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('Detalles de la Venta')
                    ->modalContent(function (Venta $record) {
                        return view('filament.resources.cliente-resource.modals.venta-detalles', [
                            'venta' => $record->load('detalles.article'),
                        ]);
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Cerrar'),
            ])
            ->bulkActions([
                //
            ]);
    }
}

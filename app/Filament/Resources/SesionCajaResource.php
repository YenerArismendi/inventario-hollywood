<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SesionCajaResource\Pages;
use App\Models\Caja;
use App\Models\SesionCaja;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class SesionCajaResource extends Resource
{
    protected static ?string $model = SesionCaja::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Ventas';
    protected static ?string $label = 'Sesión de Caja';
    protected static ?string $pluralLabel = 'Sesiones de Caja';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('caja_id')->relationship('caja', 'nombre')->required(),
                Forms\Components\Select::make('user_id')->relationship('user', 'name')->required(),
                Forms\Components\TextInput::make('monto_inicial')->numeric()->prefix('COP'),
                Forms\Components\TextInput::make('monto_final_calculado')->numeric()->prefix('COP'),
                Forms\Components\TextInput::make('monto_final_contado')->numeric()->prefix('COP'),
                Forms\Components\TextInput::make('diferencia')->numeric()->prefix('COP'),
                Forms\Components\DateTimePicker::make('fecha_apertura'),
                Forms\Components\DateTimePicker::make('fecha_cierre'),
                Forms\Components\Select::make('estado')->options([
                    'abierta' => 'Abierta',
                    'cerrada' => 'Cerrada',
                    'pendiente_aprobacion' => 'Pendiente Aprobación',
                    'aprobada' => 'Aprobada',
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('caja.nombre')->sortable(),
                Tables\Columns\TextColumn::make('user.name')->label('Responsable')->sortable(),
                Tables\Columns\TextColumn::make('monto_inicial')->money('cop')->sortable(),
                Tables\Columns\TextColumn::make('fecha_apertura')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('estado')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'abierta' => 'success',
                        'cerrada' => 'danger',
                        'pendiente_aprobacion' => 'warning',
                        'aprobada' => 'info',
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('cerrar_sesion')
                    ->label('Cerrar Sesión')
                    ->icon('heroicon-o-lock-closed')
                    ->color('warning')
                    ->visible(fn(SesionCaja $record) => $record->estado === 'abierta')
                    ->form([
                        Forms\Components\TextInput::make('monto_final_contado')
                            ->label('Monto Final Contado')
                            ->numeric()
                            ->prefix('COP')
                            ->required(),
                        Forms\Components\Textarea::make('notas_cierre')->label('Notas de Cierre'),
                    ])
                    ->action(function (SesionCaja $record, array $data) {
                        // Lógica para cerrar la caja
                        $totalVentas = $record->ventas()->sum('total');
                        $montoCalculado = $record->monto_inicial + $totalVentas;
                        $diferencia = $data['monto_final_contado'] - $montoCalculado;

                        $record->update([
                            'monto_final_calculado' => $montoCalculado,
                            'monto_final_contado' => $data['monto_final_contado'],
                            'diferencia' => $diferencia,
                            'notas_cierre' => $data['notas_cierre'],
                            'fecha_cierre' => now(),
                            'estado' => 'pendiente_aprobacion',
                        ]);
                    }),
                Tables\Actions\Action::make('aprobar_cierre')
                    ->label('Aprobar Cierre')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn(SesionCaja $record) => $record->estado === 'pendiente_aprobacion')
                    ->requiresConfirmation()
                    ->action(function (SesionCaja $record) {
                        $record->update([
                            'estado' => 'aprobada',
                            'aprobado_por_id' => auth()->id(),
                        ]);
                    }),
            ])
            ->headerActions([
                Tables\Actions\Action::make('abrir_sesion')
                    ->label('Abrir Nueva Sesión')
                    ->form([
                        Forms\Components\Select::make('caja_id')
                            ->label('Caja')
                            ->options(function () {
                                /** @var \App\Models\User $user */
                                $user = auth()->user();
                                // Si el usuario es admin, muestra todas las cajas activas del sistema.
                                if ($user->hasRole('admin')) {
                                    return Caja::where('activa', true)->pluck('nombre', 'id');
                                }
                                // 1. Obtenemos los IDs de las bodegas a las que el usuario está asignado.
                                $bodegaIds = $user->bodegas()->pluck('bodegas.id');
                                // 2. Mostramos solo las cajas ACTIVAS que pertenecen a esas bodegas.
                                return Caja::whereIn('bodega_id', $bodegaIds)
                                    ->where('activa', true)
                                    ->pluck('nombre', 'id');
                            })
                            ->searchable()
                            ->required(),
                        Forms\Components\TextInput::make('monto_inicial')
                            ->label('Monto Base Inicial')
                            ->numeric()
                            ->prefix('COP')
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        SesionCaja::create([
                            'caja_id' => $data['caja_id'],
                            'user_id' => auth()->id(),
                            'monto_inicial' => $data['monto_inicial'],
                            'fecha_apertura' => now(),
                            'estado' => 'abierta',
                        ]);
                    }),
            ]);
    }

    public static function canCreate(): bool
    {
        return false; // Deshabilitamos el botón "Crear" estándar
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSesionCajas::route('/'),
        ];
    }
}

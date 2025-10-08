<?php

namespace App\Filament\Resources;

use App\Services\SesionCajaService;
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
                Tables\Actions\Action::make('aprobar_cierre')
                    ->label('Aprobar Cierre')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn(SesionCaja $record) => $record->estado === 'pendiente_aprobacion')
                    ->requiresConfirmation()
                    ->action(function (SesionCaja $record, SesionCajaService $sesionCajaService) {
                        // Llamamos al servicio
                        $sesionCajaService->aprobarCierre($record);
                    }),
            ])
            ->headerActions([]);
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

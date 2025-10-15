<?php

namespace App\Filament\Resources;

use App\Support\ColombiaData;
use App\Filament\Resources\ClienteResource\Pages;
use App\Models\Cliente;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Validation\Rule;

class ClienteResource extends Resource
{
    protected static ?string $model = Cliente::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Ventas';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->required()->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()->maxLength(255)->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('documento_identidad')
                    ->label('Documento de Identidad')->maxLength(255)->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('telefono')
                    ->tel()->maxLength(255),
                Forms\Components\Select::make('departamento')
                    ->label('Departamento')
                    ->options(ColombiaData::getDepartamentos())
                    ->searchable()
                    ->live() // Crucial para la reactividad
                    ->afterStateUpdated(fn(Forms\Set $set) => $set('ciudad', null)), // Resetea la ciudad al cambiar de depto.

                Forms\Components\Select::make('ciudad')
                    ->label('Ciudad')
                    ->options(function (Forms\Get $get): array {
                        return ColombiaData::getCiudades($get('departamento') ?? '');
                    })
                    ->searchable()
                    ->visible(fn(Forms\Get $get) => !empty($get('departamento'))), // Solo visible si se ha elegido un depto.
                Forms\Components\TextInput::make('direccion')
                    ->maxLength(255),
                Forms\Components\Toggle::make('tiene_credito')
                    ->label('¿Tiene crédito?')
                    ->live()
                    ->reactive(),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\TextInput::make('limite_credito')
                            ->label('Límite de Crédito')
                            ->prefix('COP')
                            ->mask(\Filament\Support\RawJs::make('$money($input, \'.\')'))
                            ->rule(function () {
                                return function (string $attribute, $value, \Closure $fail) {
                                    $cleanedValue = str_replace([',', '.'], '', $value);
                                    if (!is_numeric($cleanedValue)) {
                                        $fail('El campo :attribute debe ser numérico.');
                                    }
                                };
                            })
                            ->dehydrateStateUsing(fn($state) => str_replace([',', '.'], '', $state ?? ''))
                            ->default(0),
                        Forms\Components\TextInput::make('dias_credito')
                            ->label('Días de Crédito')
                            ->numeric()
                            ->default(0),
                    ])->visible(fn(Forms\Get $get) => $get('tiene_credito')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('documento_identidad')
                    ->searchable(),
                Tables\Columns\TextColumn::make('telefono')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\IconColumn::make('tiene_credito')
                    ->boolean(),
                Tables\Columns\TextColumn::make('limite_credito')
                    ->money('cop')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClientes::route('/'),
            'create' => Pages\CreateCliente::route('/create'),
            'edit' => Pages\EditCliente::route('/{record}/edit'),
        ];
    }
}

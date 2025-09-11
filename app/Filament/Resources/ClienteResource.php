<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClienteResource\Pages;
use App\Filament\Resources\ClienteResource\RelationManagers;
use App\Models\Cliente;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
                    ->email()->maxLength(255),
                Forms\Components\TextInput::make('documento_identidad')
                    ->label('Documento de Identidad')->maxLength(255),
                Forms\Components\TextInput::make('telefono')
                    ->tel()->maxLength(255),
                Forms\Components\TextInput::make('ciudad')
                    ->maxLength(255),
                Forms\Components\TextInput::make('direccion')
                    ->maxLength(255),
                Forms\Components\Toggle::make('tiene_credito')
                    ->label('¿Tiene crédito?')
                    ->live(), // Usamos live() en lugar de reactive() para Filament v3
                Forms\Components\TextInput::make('limite_credito')
                    ->label('Límite de Crédito')
                    ->numeric()
                    ->prefix('COP')
                    ->default(0)
                    ->visible(fn(Forms\Get $get) => $get('tiene_credito')),
                Forms\Components\TextInput::make('dias_credito')
                    ->label('Días de Crédito')
                    ->numeric()
                    ->default(0)
                    ->visible(fn(Forms\Get $get) => $get('tiene_credito')),
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

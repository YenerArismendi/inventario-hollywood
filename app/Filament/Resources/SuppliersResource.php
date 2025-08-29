<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SuppliersResource\Pages;
use App\Filament\Resources\SuppliersResource\RelationManagers;
use App\Models\Suppliers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SuppliersResource extends Resource
{
    protected static ?string $model = Suppliers::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    public static function getModelLabel(): string
    {
        return 'Proveedores';
    }

    public static function getNavigationSort(): ?int
    {
        return 1; // Cuanto menor el número, más arriba aparece
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->afterStateUpdated(fn($state, callable $set) => $set('name', strtolower($state)))
                    ->label('Nombre del proveedor'),
                Forms\Components\TextInput::make('responsible')
                    ->afterStateUpdated(fn($state, callable $set) => $set('responsible', strtolower($state)))
                    ->label('Responsable'),
                Forms\Components\TextInput::make('email')
                    ->afterStateUpdated(fn($state, callable $set) => $set('email', strtolower($state)))
                    ->label('Correo'),
                Forms\Components\TextInput::make('phone')
                    ->label('Telefono'),
                Forms\Components\TextInput::make('address')
                    ->afterStateUpdated(fn($state, callable $set) => $set('address', strtolower($state)))
                    ->label('Dirección'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->limit(20)
                    ->tooltip(fn($record) => $record->name),
                Tables\Columns\TextColumn::make('responsible')
                    ->label('Responsable')
                    ->limit(20)
                    ->tooltip(fn($record) => $record->responsible),
                Tables\Columns\TextColumn::make('email')
                    ->label('Correo')
                    ->limit(20)
                    ->tooltip(fn($record) => $record->email),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Telefono'),
                Tables\Columns\TextColumn::make('address')
                    ->label('Dirección')
                    ->limit(10)
                    ->tooltip(fn($record) => $record->address),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListSuppliers::route('/'),
            'create' => Pages\CreateSuppliers::route('/create'),
            'edit' => Pages\EditSuppliers::route('/{record}/edit'),
        ];
    }
}

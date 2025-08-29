<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BodegaResource\Pages;
use App\Filament\Resources\BodegaResource\RelationManagers;
use App\Models\Bodega;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BodegaResource extends Resource
{
    protected static ?string $model = Bodega::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->required()
                    ->label('Nombre de la Bodega'),
                Forms\Components\TextInput::make('direccion')
                    ->label('Dirección'),
                Select::make('tipo')
                    ->label('Tipo de Bodega')
                    ->options([
                        'almacen' => 'Almacén General',
                        'fabrica' => 'Fábrica de Perfumes',
                    ])
                    ->required()
                    ->native(false),
                Select::make('encargado_id')
                    ->label('Encargado de Bodega')
                    ->relationship(name: 'encargado', titleAttribute: 'name')
                    ->getOptionLabelFromRecordUsing(fn (User $record) => "{$record->name} - ({$record->getRoleNames()->join(', ')})")
                    ->searchable()
                    ->preload(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('direccion')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tipo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('encargado.name')
                    ->label('Encargado')
                    ->searchable()
                    ->sortable()
                    ->default('No asignado'),
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
                    Tables\Actions\DeleteBulkAction::make()
                    ->visible(fn (User $user) => $user->can('delete_any_bodega')),
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
            'index' => Pages\ListBodegas::route('/'),
            'create' => Pages\CreateBodega::route('/create'),
            'edit' => Pages\EditBodega::route('/{record}/edit'),
        ];
    }
}

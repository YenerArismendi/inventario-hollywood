<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RecetasResource\Pages;
use App\Models\Article;
use App\Models\Recetas;
use App\Models\Articulo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class RecetasResource extends Resource
{
    protected static ?string $model = Recetas::class;

    protected static ?string $navigationIcon = 'heroicon-o-beaker';

    public static function getNavigationGroup(): ?string
    {
        return 'Preparaciones';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Tabs::make('Receta')
                ->tabs([

                    // 🧾 TAB 1: Información general
                    Forms\Components\Tabs\Tab::make('Información General')
                        ->icon('heroicon-o-document-text')
                        ->schema([
                            TextInput::make('nombre')
                                ->label('Nombre de la Receta')
                                ->prefixIcon('heroicon-o-book-open')
                                ->required(),

                            Select::make('tipo')
                                ->label('Tipo de Receta')
                                ->prefixIcon('heroicon-o-beaker')
                                ->options([
                                    'california' => 'California',
                                    'acrilico' => 'Acrílico',
                                    'potes' => 'Potes',
                                    'splash' => 'Splash',
                                ])
                                ->required(),

                            TextInput::make('precio')
                                ->label('Precio Venta')
                                ->prefixIcon('heroicon-o-currency-dollar')
                                ->required(),


                        ])
                        ->columns(2),

                    // 🧪 TAB 2: Componentes de la receta
                    Forms\Components\Tabs\Tab::make('Componentes')
                        ->icon('heroicon-o-beaker')
                        ->schema([
                            Card::make()
                                ->schema([
                                    Repeater::make('detalles')
                                        ->relationship('detalles')
                                        ->label('Componentes de la Receta')
                                        ->schema([
                                            Select::make('insumos_id')
                                                ->relationship(
                                                    name: 'insumo',
                                                    titleAttribute: 'nombre',
                                                )
                                                ->label('Insumo')
                                                ->searchable()
                                                ->required()
                                                ->preload()
                                                ->columnSpan(3),

                                            TextInput::make('cantidad')
                                                ->label('Cantidad Usada')
                                                ->numeric()
                                                ->required()
                                                ->columnSpan(3),

                                            Select::make('unidad')
                                                ->label('Unidad')
                                                ->options([
                                                    'mililitros' => 'Mililitros',
                                                    'unidad' => 'Unidad',
                                                ])
                                                ->columnSpan(2),

                                        ])
                                        ->addActionLabel('Agregar Insumo')
                                        ->columns(12)
                                        ->grid(1)
                                        ->columnSpanFull()
                                        ->reorderable(false),
                                ])
                                ->columnSpanFull()
                                ->extraAttributes([
                                    'class' => 'bg-white shadow-sm rounded-2xl p-4 border border-gray-200',
                                ]),
                        ]),
                ])
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Nombre de la Receta')
                    ->searchable()
                    ->sortable()
                    ->limit(25),

                Tables\Columns\TextColumn::make('tipo')
                    ->label('Tipo')
                    ->sortable()
                    ->badge() // Muestra un estilo tipo etiqueta
                    ->color(fn(string $state): string => match ($state) {
                        'california' => 'success',
                        'acrilico' => 'info',
                        'potes' => 'warning',
                        'splash' => 'primary',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('precio')
                    ->label('Precio Venta')
                    ->formatStateUsing(fn($state) => '$' . number_format($state, 0, ',', '.'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('detalles.insumo')
                    ->label('Insumos')
                    ->formatStateUsing(function ($state, $record) {
                        // Si no hay relación detalles, devolvemos texto vacío
                        if (!$record->detalles) {
                            return '—';
                        }

                        $insumos = $record->detalles->pluck('insumo.nombre')->filter()->toArray();

                        if (empty($insumos)) {
                            return '—';
                        }

                        // Mostramos hasta 3 insumos
                        return implode(', ', array_slice($insumos, 0, 3))
                            . (count($insumos) > 3 ? '...' : '');
                    })
                    ->tooltip(function ($record) {
                        if (!$record->detalles) {
                            return null;
                        }

                        return $record->detalles->pluck('insumo.nombre')->filter()->join(', ');
                    })
                    ->wrap()
                    ->limit(30),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRecetas::route('/'),
            'create' => Pages\CreateRecetas::route('/create'),
            'edit' => Pages\EditRecetas::route('/{record}/edit'),
        ];
    }
}

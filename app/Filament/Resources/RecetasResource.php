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

                            TextInput::make('descripcion')
                                ->label('Descripción')
                                ->prefixIcon('heroicon-o-pencil-square')
                                ->columnSpanFull(),

                            Select::make('articulo_final_id')
                                ->label('Artículo Final')
                                ->prefixIcon('heroicon-o-cube')
                                ->searchable()
                                ->relationship(
                                    name: 'articuloFinal',
                                    titleAttribute: 'nombre',
                                    modifyQueryUsing: fn($query) => $query->where('tipo', 'producto')
                                )
                                ->preload()
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
                                            Select::make('articulo_id')
                                                ->relationship(
                                                    name: 'articulo',
                                                    titleAttribute: 'nombre',
                                                    modifyQueryUsing: fn($query) => $query->where('tipo', 'insumo')
                                                )
                                                ->label('Insumo')
                                                ->searchable()
                                                ->required()
                                                ->reactive()
                                                ->afterStateUpdated(function ($state, callable $set) {
                                                    // Cuando cambia el insumo, traemos sus datos
                                                    $articulo = Article::find($state);
                                                    if ($articulo) {
                                                        $set('precio_unitario', $articulo->precio);
                                                        $set('presentacion', $articulo->cantidad_total);
                                                    } else {
                                                        $set('precio_unitario', null);
                                                        $set('presentacion', null);
                                                        $set('costo_total', null);
                                                    }
                                                })
                                                ->columnSpan(3),

                                            TextInput::make('cantidad')
                                                ->label('Cantidad Usada')
                                                ->numeric()
                                                ->required()
                                                ->reactive()
                                                ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                                    $precio_unitario = $get('precio_unitario');
                                                    $presentacion = $get('presentacion');

                                                    if ($precio_unitario && $presentacion && $state) {
                                                        $costo_total = ($precio_unitario / $presentacion) * $state;
                                                        $set('costo_total', round($costo_total, 2));
                                                    } else {
                                                        $set('costo_total', null);
                                                    }
                                                })
                                                ->columnSpan(3),

                                            TextInput::make('precio_unitario')
                                                ->label('Precio Unitario')
                                                ->numeric()
                                                ->disabled()
                                                ->dehydrated(false)
                                                ->columnSpan(2),

                                            TextInput::make('presentacion')
                                                ->label('Presentación')
                                                ->numeric()
                                                ->disabled()
                                                ->dehydrated(false)
                                                ->columnSpan(2),

                                            TextInput::make('costo_total')
                                                ->label('Costo Total')
                                                ->numeric()
                                                ->disabled(false) // Se guarda en BD
                                                ->suffix('COP')
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
                    ->label('Nombre')
                    ->limit(20)
                    ->searchable(),

                Tables\Columns\TextColumn::make('descripcion')
                    ->label('Descripción')
                    ->limit(20)
                    ->searchable(),

                Tables\Columns\TextColumn::make('articuloFinal.nombre')
                    ->label('Artículo Final')
                    ->limit(20)
                    ->searchable(),

                Tables\Columns\TagsColumn::make('detalles.articulo.nombre')
                    ->label('Insumos')
                    ->limit(3)
                    ->separator(','),
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

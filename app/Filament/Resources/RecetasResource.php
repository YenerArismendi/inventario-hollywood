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
            ->defaultSort('created_at', 'desc') // 🔹 Ordena las recetas más recientes primero
            ->columns([

                Tables\Columns\TextColumn::make('nombre')
                    ->label('Nombre de la Receta')
                    ->icon('heroicon-o-book-open')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->limit(25),

                Tables\Columns\TextColumn::make('tipo')
                    ->label('Tipo')
                    ->icon('heroicon-o-beaker')
                    ->badge()
                    ->sortable()
                    ->color(fn(string $state): string => match ($state) {
                        'california' => 'success',
                        'acrilico' => 'info',
                        'potes' => 'warning',
                        'splash' => 'primary',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('precio')
                    ->label('💲 Precio Venta')
                    ->formatStateUsing(fn($state) => '$' . number_format($state, 0, ',', '.'))
                    ->sortable()
                    ->color('success')
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('costo_total')
                    ->label('💰 Costo Total')
                    ->formatStateUsing(fn($record) => '$' . number_format($record->costo_total, 0, ',', '.'))
                    ->sortable()
                    ->color('warning'),

                Tables\Columns\TextColumn::make('resultado')
                    ->label('📊 Resultado')
                    ->getStateUsing(function ($record) {
                        $precioVenta = (float) $record->precio;
                        $costoTotal = (float) $record->costo_total; // usa el accessor del modelo
                        $diferencia = $precioVenta - $costoTotal;

                        if ($diferencia > 0) {
                            return 'Ganancia: $' . number_format($diferencia, 0, ',', '.');
                        } elseif ($diferencia < 0) {
                            return 'Pérdida: $' . number_format(abs($diferencia), 0, ',', '.');
                        } else {
                            return 'Sin ganancia';
                        }
                    })
                    ->badge()
                    ->icon(
                        fn($record) =>
                        $record->precio > $record->costo_total
                            ? 'heroicon-o-arrow-trending-up'
                            : ($record->precio < $record->costo_total
                                ? 'heroicon-o-arrow-trending-down'
                                : 'heroicon-o-minus')
                    )
                    ->color(
                        fn($record) =>
                        $record->precio > $record->costo_total
                            ? 'success'
                            : ($record->precio < $record->costo_total
                                ? 'danger'
                                : 'gray')
                    )
                    ->sortable(),

                Tables\Columns\TextColumn::make('detalles.insumo')
                    ->label('🧪 Insumos')

                    ->formatStateUsing(function ($state, $record) {
                        if (!$record->relationLoaded('detalles')) {
                            $record->load('detalles.insumo');
                        }

                        if (!$record->detalles || $record->detalles->isEmpty()) {
                            return '—';
                        }

                        $insumos = $record->detalles->pluck('insumo.nombre')->filter()->toArray();

                        // Mostramos máximo 3 insumos
                        $texto = implode(', ', array_slice($insumos, 0, 3))
                            . (count($insumos) > 3 ? '...' : '');

                        // Agregamos el ícono del ojo como botón
                        $url = route('filament.admin.resources.recetas.view', $record);
                        $icono = '<a href="' . $url . '" 
                    class="inline-flex items-center text-blue-600 hover:text-blue-800 ml-2"
                    title="Ver receta">
                    <x-heroicon-o-eye class="w-4 h-4"/>
                  </a>';

                        return $texto . $icono;
                    })
                    ->tooltip(function ($record) {
                        if (!$record->detalles) return null;
                        return $record->detalles->pluck('insumo.nombre')->filter()->join(', ');
                    })
                    ->wrap()
                    ->html() // 🔹 Importante: permite renderizar el enlace con el ícono
                    ->limit(30)
                    ->color('gray'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('📅 Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->color('gray'),

            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Ver')
                    ->icon('heroicon-o-eye')
                    ->color('info'),

                Tables\Actions\EditAction::make()
                    ->label('Editar')
                    ->icon('heroicon-o-pencil-square')
                    ->color('warning'),

                Tables\Actions\DeleteAction::make()
                    ->label('Eliminar')
                    ->icon('heroicon-o-trash')
                    ->color('danger'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Eliminar seleccionados'),
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

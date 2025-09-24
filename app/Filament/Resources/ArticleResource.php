<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticleResource\Pages;
use App\Models\Article;
use App\Models\Suppliers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

//use App\Filament\Widgets\ArticleVariantsStats;
use Illuminate\Database\Eloquent\Builder;

class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;
    protected static ?string $navigationIcon = 'heroicon-o-cube';

    public static function getModelLabel(): string
    {
        return 'Artículos';
    }

    protected $listeners = ['refresh' => '$refresh'];

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('nombre')
                ->label('Nombre del producto')
                ->reactive()
                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                    $tipoDetalle = $get('tipo_detalle');
                    if (!empty($state) && !empty($tipoDetalle)) {
                        $codigo = \App\Helpers\ProductHelper::generarCodigoProducto($state, $tipoDetalle);
                        $set('codigo', $codigo);
                    }
                }),
            Forms\Components\Select::make('tipo')
                ->options([
                    'producto' => 'Producto',
                    'insumo' => 'Insumo',
                ])
                ->label('Tipo')
                ->required()
                ->reactive(), // ← importante para refrescar dependencias

            // Nuevo campo condicional
            Forms\Components\Select::make('tipo_detalle')
                ->label('Detalle según tipo')
                ->options(function (callable $get) {
                    if ($get('tipo') === 'producto') {
                        return [
                            'fragancia' => 'Fragancia',
                            'bolso' => 'Bolso',
                            'crema' => 'Crema',
                        ];
                    } elseif ($get('tipo') === 'insumo') {
                        return [
                            'envase' => 'Envase',
                            'tapa' => 'Tapa',
                            'etiqueta' => 'Etiqueta',
                        ];
                    }
                    return [];
                })
                ->visible(fn(callable $get) => in_array($get('tipo'), ['producto', 'insumo']))
                ->reactive()
                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                    $nombre = $get('nombre');
                    if (!empty($state) && !empty($nombre)) {
                        $codigo = \App\Helpers\ProductHelper::generarCodigoProducto($nombre, $state);
                        $set('codigo', $codigo);
                    }
                })
                ->required(),

            Forms\Components\TextInput::make('descripcion')
                ->label('Descripción')
                ->afterStateUpdated(fn($state, callable $set) => $set('descripcion', strtolower($state))),

            Forms\Components\TextInput::make('precio')
                ->label('Precio')
                ->numeric()
                ->suffix('COP'),

            Forms\Components\Select::make('unidad_medida')
                ->options([
                    'kilo' => 'Kilo',
                    'unidad' => 'Unidad',
                    'litro' => 'Litro',
                    'caja' => 'Caja',
                    'mililitro' => 'Mililitro',
                ])
                ->label('Unidad')
                ->required(),

            Forms\Components\Select::make('proveedor_id')
                ->label('Proveedor')
                ->relationship('proveedor', 'name')
                ->searchable()
                ->preload()
                ->createOptionForm([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->label('Nombre del proveedor'),
                    Forms\Components\TextInput::make('phone')->label('Teléfono'),
                    Forms\Components\TextInput::make('responsible')->label('Responsable'),
                    Forms\Components\TextInput::make('email')->email()->label('Correo'),
                    Forms\Components\TextInput::make('address')->label('Dirección'),
                ])
                ->createOptionUsing(function (array $data) {
                    return Suppliers::create([
                        'name' => $data['name'] ?? 'Sin nombre',
                        'phone' => $data['phone'] ?? null,
                        'responsible' => $data['responsible'] ?? null,
                        'email' => $data['email'] ?? null,
                        'address' => $data['address'] ?? null,
                    ])->getKey();
                }),

            Forms\Components\Select::make('temporada')
                ->label('Temporada de ventas')
                ->options([
                    'enero_rebajas' => 'Enero – Rebajas de inicio de año',
                    'febrero_san_valentin' => 'Febrero – San Valentín',
                    'marzo_dia_mujer' => 'Marzo – Día de la Mujer',
                    'abril_semana_santa' => 'Abril – Semana Santa',
                    'mayo_dia_madre' => 'Mayo – Día de la Madre',
                    'junio_padre_midyear' => 'Junio – Día del Padre / Mitad de año',
                    'julio_festivo' => 'Julio – Festivos de mitad de año',
                    'agosto_regreso_clases' => 'Agosto – Regreso a clases',
                    'septiembre_amor_amistad' => 'Septiembre – Amor y Amistad',
                    'octubre_halloween' => 'Octubre – Halloween',
                    'noviembre_black_friday' => 'Noviembre – Black Friday / Cyber Lunes',
                    'diciembre_navidad' => 'Diciembre – Navidad y Fin de año',
                ])
                ->searchable()
                ->placeholder('Selecciona una temporada')
                ->reactive()
                ->visible(fn(callable $get) => $get('tipo') === 'producto'), // 👈 aquí la magia,

            Forms\Components\TextInput::make('codigo')
                ->label('Código del producto')
                ->readOnly(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Nombre')
                    ->limit(20)
                    ->tooltip(fn($record) => $record->nombre)
                    ->searchable(), // 👈 habilita búsqueda en esta columna

                Tables\Columns\TextColumn::make('tipo')
                    ->label('Tipo')
                    ->searchable(), // 👈 también aquí

                Tables\Columns\TextColumn::make('codigo')
                    ->label('Código')
                    ->searchable(),

                Tables\Columns\TextColumn::make('descripcion')
                    ->label('Descripción')
                    ->limit(20)
                    ->searchable(),

                Tables\Columns\TextColumn::make('precio')
                    ->label('Precio')
                    ->formatStateUsing(function ($state) {
                        if ($state < 100) {
                            return number_format($state, 2, ',', '.') . ' COP';
                        }
                        return number_format($state, 0, ',', '.') . ' COP';
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('unidad_medida')
                    ->label('Unidad'),

                Tables\Columns\TextColumn::make('proveedor.name')
                    ->label('Proveedor')
                    ->searchable(), // 👈 importante si quieres buscar por proveedor
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->searchPlaceholder('Buscar artículo...'); // 👈 texto del buscador
    }


    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();
        $query = parent::getEloquentQuery();

        if (!$user || $user->hasRole('admin')) {
            return $query;
        }

        if ($user->active_bodega_id) {
            $query->whereHas('bodegas', function (Builder $q) use ($user) {
                $q->where('bodega_article.bodega_id', $user->active_bodega_id);
            });
        } else {
            $query->whereRaw('0 = 1');
        }

        return $query;
    }

    public static function getRelations(): array
    {
        return [
            // Aquí podrías agregar un RelationManager para variantes si quieres editarlas inline
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListArticles::route('/'),
            'create' => Pages\CreateArticle::route('/create'),
            'edit' => Pages\EditArticle::route('/{record}/edit'),
        ];
    }
}

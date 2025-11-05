<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticleResource\Pages;
use App\Models\Article;
use App\Models\Category;
use App\Models\Suppliers;
use App\Models\Brand;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
                ->live(onBlur: true),
            Forms\Components\TextInput::make('presentation')
                ->label('Presentación (Ej: 120ml, 200g, Cartera Mediana)')
                ->reactive()
                ->live(onBlur: true),
            Forms\Components\TextInput::make('codigo_barras')
                ->label('Código de Barras (Opcional)')
                ->unique(Article::class, 'codigo_barras', ignoreRecord: true)
                ->placeholder('Escanear o digitar código de barras...'),

            Forms\Components\Select::make('category_id')
                ->label('Categoría')
                ->relationship('category', 'name')
                ->searchable()
                ->preload()
                ->createOptionForm([
                    Forms\Components\TextInput::make('name')
                        ->required()->label('Nombre de la categoría'),
                ])
                ->required()
                ->reactive(),
            Forms\Components\Select::make('brand_id')
                ->label('Marca')
                ->relationship('brand', 'name')
                ->searchable()
                ->preload()
                ->createOptionForm([
                    Forms\Components\TextInput::make('name')
                        ->required()->label('Nombre de la marca'),
                ])->required(),

            Forms\Components\TextInput::make('descripcion')
                ->label('Descripción')
                ->afterStateUpdated(fn($state, callable $set) => $set('descripcion', strtolower($state))),

            Forms\Components\TextInput::make('costo')
                ->label('Costo (Opcional)')
                ->numeric()
                ->suffix('COP'),

            Forms\Components\TextInput::make('precio_venta')
                ->label('Precio de Venta')
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
                ->placeholder('Selecciona una temporada'),

            Forms\Components\TextInput::make('codigo')
                ->label('Código del producto')
                ->placeholder('Se Genera Automaticamente')
                ->readOnly(),

            // Campo para mostrar el código QR generado
            Forms\Components\ViewField::make('codigo_qr')
                ->label('Código QR')
                ->view('filament.resources.article-resource.fields.qr-code')
                ->visibleOn('edit'), // Solo visible en la página de edición
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

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Categoría')
                    ->searchable(),

                Tables\Columns\TextColumn::make('codigo')
                    ->label('Código')
                    ->searchable(),

                Tables\Columns\TextColumn::make('descripcion')
                    ->label('Descripción')
                    ->limit(20)
                    ->searchable(),

                Tables\Columns\TextColumn::make('codigo_barras')
                    ->label('Cód. Barras')
                    ->searchable(),

                Tables\Columns\TextColumn::make('precio_venta')
                    ->label('Precio Venta')
                    ->formatStateUsing(function ($state) {
                        if ($state < 100) {
                            return number_format($state, 2, ',', '.') . ' COP';
                        }
                        return number_format($state, 0, ',', '.') . ' COP';
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('bodegas_sum_stock')
                    ->label('Stock Total')
                    ->numeric()
                    ->sortable()
                    ->searchable(),

            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('detalles')
                    ->label('')
                    ->icon('heroicon-o-information-circle')
                    ->modalContent(fn(Article $record): \Illuminate\Contracts\View\View => view('filament.resources.article-resource.modals.stock-details', ['record' => $record])
                    )
                    ->modalHeading(fn(Article $record) => 'Stock de ' . $record->nombre)
                    ->modalCancelActionLabel('Cerrar')
                    ->modalSubmitAction(false)
                    ->action(null),

                Tables\Actions\EditAction::make()
                    ->label(''),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->searchPlaceholder('Buscar artículo...'); // texto del buscador
    }


    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();
        $query = parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class, // 1. Le decimos a Filament que considere los artículos archivados
            ])
            ->select('articles.*') // 2. Nos aseguramos de seleccionar todas las columnas de articles
            ->selectSub( // 3. Añadimos tu subconsulta para el stock total
                'select sum(stock) from bodega_article where article_id = articles.id',
                'bodegas_sum_stock'
            );

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

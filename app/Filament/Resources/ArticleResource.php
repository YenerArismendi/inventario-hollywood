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
use App\Filament\Widgets\ArticleVariantsStats;

class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;
    protected static ?string $navigationIcon = 'heroicon-o-cube';

    public static function getModelLabel(): string
    {
        return 'Artículos';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('nombre')
                ->label('Nombre del producto')
                ->reactive()
                ->afterStateUpdated(function ($state, callable $set) {
                    if (!empty($state)) {
                        $codigo = \App\Helpers\ProductHelper::generarCodigoProducto($state);
                        $set('codigo', $codigo);
                    }
                })
                ->afterStateUpdated(fn ($state, callable $set) => $set('nombre', strtolower($state))), // ← corregido
            Forms\Components\Select::make('tipo')
                ->options([
                    'producto' => 'Producto',
                    'insumo' => 'Insumo',
                ])
                ->label('Tipo')
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
                    ->tooltip(fn($record) => $record->nombre),
                Tables\Columns\TextColumn::make('tipo')->label('Tipo'),
                Tables\Columns\TextColumn::make('codigo')->label('Código'),
                Tables\Columns\TextColumn::make('descripcion')->label('Descripción')->limit(20),
                Tables\Columns\TextColumn::make('precio')->label('Precio'),
                Tables\Columns\TextColumn::make('unidad_medida')->label('Unidad'),
                Tables\Columns\TextColumn::make('proveedor.name')->label('Proveedor'),
                Tables\Columns\TextColumn::make('variantes_count')
                    ->label('Variantes')
                    ->counts('variantes'), // <-- Esto usa withCount en la relación
            ])
            ->filters([])
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

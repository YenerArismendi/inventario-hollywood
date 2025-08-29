<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticleResource\Pages;
use App\Filament\Resources\ArticleResource\RelationManagers;
use App\Helpers\ProductHelper;
use App\Models\Article;
use App\Models\Suppliers;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
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
        return 'Articulos';
    }

    public static function form(Form $form): Form
    {

        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->label('Nombre del producto')
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if (!empty($state)) {
                            $codigo = ProductHelper::generarCodigoProducto($state);
                            $set('codigo', $codigo);
                        }
                    })
                    ->afterStateUpdated(fn($state, callable $set) => $set('name', strtolower($state))),
                Forms\Components\Select::make('tipo')
                    ->options([
                        'producto' => 'Producto',
                        'insumo' => 'Insumo',
                    ])
                    ->label('Tipo')
                    ->required(),
                Forms\Components\TextInput::make('descripcion')
                    ->label('Descripcion')
                    ->afterStateUpdated(fn($state, callable $set) => $set('descripcion', strtolower($state))),
                Forms\Components\TextInput::make('precio')
                    ->label('Precio'),
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
//                            ->afterStateUpdated(fn($state, callable $set) => $set('name', strtolower($state))),
                        Forms\Components\TextInput::make('phone')->label('Teléfono'),
//                            ->afterStateUpdated(fn($state, callable $set) => $set('telefono', strtolower($state))),
                        Forms\Components\TextInput::make('responsible')->label('Responsable'),
//                            ->afterStateUpdated(fn ($state, callable $set) => $set('responsible', strtolower($state))),
                        Forms\Components\TextInput::make('email')->email()->label('Correo'),
//                            ->afterStateUpdated(fn ($state, callable $set) => $set('email', strtolower($state))),
                        Forms\Components\TextInput::make('address')->label('Direccion'),
//                            ->afterStateUpdated(fn ($state, callable $set) => $set('addres', strtolower($state))),
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
                    ->readOnly(), // Para que no lo editen manualmente
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
                Tables\Columns\TextColumn::make('tipo')
                    ->label('Tipo')
                    ->limit(20)
                    ->tooltip(fn($record) => $record->tipo),
                Tables\Columns\TextColumn::make('codigo')
                    ->label('Codigo')
                    ->limit(20)
                    ->tooltip(fn($record) => $record->codigo),
                Tables\Columns\TextColumn::make('descripcion')
                    ->label('Descripcion')
                    ->limit(20)
                    ->tooltip(fn($record) => $record->descripcion),
                Tables\Columns\TextColumn::make('precio')
                    ->label('Precio')
                    ->limit(20)
                    ->tooltip(fn($record) => $record->precio),
                Tables\Columns\TextColumn::make('unidad_medida')
                    ->label('Medida')
                    ->limit(20)
                    ->tooltip(fn($record) => $record->unidad_medida),
                Tables\Columns\TextColumn::make('proveedor.name')
                    ->label('Proveedor')
                    ->limit(20)
                    ->tooltip(fn($record) => $record->proveedor?->name),
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
            'index' => Pages\ListArticles::route('/'),
            'create' => Pages\CreateArticle::route('/create'),
            'edit' => Pages\EditArticle::route('/{record}/edit'),
        ];
    }
}

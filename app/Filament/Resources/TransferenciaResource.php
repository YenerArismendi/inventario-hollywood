<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransferenciaResource\Pages;
use App\Filament\Resources\TransferenciaResource\RelationManagers;
use App\Models\Transferencia;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TransferenciaResource extends Resource
{
    protected static ?string $model = Transferencia::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información de la Transferencia')
                    ->schema([
                        Forms\Components\Select::make('bodega_origen_id')
                            ->label('Bodega Origen')
                            ->relationship('bodegaOrigen', 'nombre')
                            ->default(fn() => auth()->user()->active_bodega_id ?? auth()->user()->bodegas()->first()?->id)
                            ->required()
                            ->reactive() // Make reactive to trigger validation updates if needed
                            ->searchable()
                            ->preload()
                            ->columnSpan(1),
                        Forms\Components\Select::make('bodega_destino_id')
                            ->label('Bodega Destino')
                            ->relationship('bodegaDestino', 'nombre')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->columnSpan(1),
                    ])->columns(2),

                Forms\Components\Section::make('Artículos / Insumos a Transferir')
                    ->schema([
                        Forms\Components\Repeater::make('detalles')
                            ->relationship()
                            ->schema([
                                Forms\Components\Grid::make(4)
                                    ->schema([
                                        Forms\Components\Select::make('tipo_item')
                                            ->label('Tipo')
                                            ->options([
                                                'articulo' => 'Artículo',
                                                'insumo' => 'Insumo',
                                            ])
                                            ->required()
                                            ->reactive()
                                            ->dehydrated(false)
                                            ->afterStateUpdated(fn(Forms\Set $set) => $set('article_id', null) ?? $set('insumo_id', null))
                                            ->columnSpan(1),

                                        Forms\Components\Select::make('article_id')
                                            ->label('Artículo')
                                            ->relationship('article', 'nombre')
                                            ->required(fn(Forms\Get $get) => $get('tipo_item') === 'articulo')
                                            ->visible(fn(Forms\Get $get) => $get('tipo_item') === 'articulo' || !$get('tipo_item'))
                                            ->searchable()
                                            ->preload()
                                            ->columnSpan(2),

                                        Forms\Components\Select::make('insumo_id')
                                            ->label('Insumo')
                                            ->relationship('insumo', 'nombre')
                                            ->required(fn(Forms\Get $get) => $get('tipo_item') === 'insumo')
                                            ->visible(fn(Forms\Get $get) => $get('tipo_item') === 'insumo')
                                            ->searchable()
                                            ->preload()
                                            ->columnSpan(2),

                                        Forms\Components\TextInput::make('cantidad_enviada')
                                            ->label('Cantidad')
                                            ->numeric()
                                            ->required()
                                            ->minValue(1)
                                            ->rule(function (Forms\Get $get) {
                                                return function (string $attribute, $value, \Closure $fail) use ($get) {
                                                    $bodegaOrigenId = $get('../../bodega_origen_id');
                                                    $tipoItem = $get('tipo_item');
                                                    $articleId = $get('article_id');
                                                    $insumoId = $get('insumo_id');

                                                    if (!$bodegaOrigenId) {
                                                        return; // No validar si no hay bodega origen
                                                    }

                                                    $stockDisponible = 0;

                                                    if ($tipoItem === 'articulo' && $articleId) {
                                                        $stockDisponible = \Illuminate\Support\Facades\DB::table('bodega_article')
                                                            ->where('bodega_id', $bodegaOrigenId)
                                                            ->where('article_id', $articleId)
                                                            ->value('stock') ?? 0;
                                                    } elseif ($tipoItem === 'insumo' && $insumoId) {
                                                        $stockDisponible = \Illuminate\Support\Facades\DB::table('bodega_insumo')
                                                            ->where('bodega_id', $bodegaOrigenId)
                                                            ->where('insumo_id', $insumoId)
                                                            ->value('stock') ?? 0;
                                                    }

                                                    if ($value > $stockDisponible) {
                                                        $fail("La cantidad supera el stock disponible en la bodega de origen ({$stockDisponible}).");
                                                    }
                                                };
                                            })
                                            ->columnSpan(1),
                                    ])
                            ])
                            ->columns(1)
                            ->label('Lista de ítems'),
                    ]),

                Forms\Components\Section::make('Despacho (Evidencia)')
                    ->schema([
                        Forms\Components\FileUpload::make('evidencia_despacho')
                            ->label('Fotos de Despacho (Opcional)')
                            ->multiple()
                            ->image()
                            ->directory('transferencias/despacho'),
                        Forms\Components\Textarea::make('observaciones_despacho')
                            ->label('Notas de Despacho'),
                    ])->visible(fn($record) => !$record || $record->estado === 'borrador'),

                Forms\Components\Hidden::make('user_despacho_id')
                    ->default(\Illuminate\Support\Facades\Auth::id()),

                Forms\Components\Section::make('Recepción (Evidencia)')
                    ->schema([
                        Forms\Components\FileUpload::make('evidencia_recepcion')
                            ->label('Fotos de Recepción')
                            ->multiple()
                            ->image()
                            ->directory('transferencias/recepcion')
                            ->disabled(),
                        Forms\Components\Textarea::make('observaciones_recepcion')
                            ->label('Notas de Recepción')
                            ->disabled(),
                    ])->visible(fn($record) => $record && in_array($record->estado, ['recibido_conforme', 'en_disputa', 'completado'])),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('bodegaOrigen.nombre')
                    ->label('Desde')
                    ->searchable(),
                Tables\Columns\TextColumn::make('bodegaDestino.nombre')
                    ->label('Hacia')
                    ->searchable(),
                Tables\Columns\TextColumn::make('estado')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'borrador' => 'gray',
                        'despachado' => 'warning',
                        'recibido_conforme', 'completado' => 'success',
                        'en_disputa' => 'danger',
                        'cancelado' => 'gray',
                        default => 'primary',
                    })
                    ->formatStateUsing(fn($state) => match ($state) {
                        'borrador' => 'Borrador',
                        'despachado' => 'Enviado',
                        'recibido_conforme', 'completado' => 'Completado',
                        'en_disputa' => 'En Disputa',
                        'cancelado' => 'Cancelado',
                        default => $state
                    }),
                Tables\Columns\TextColumn::make('fecha_despacho')
                    ->label('Fecha Envío')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('userDespacho.name')
                    ->label('Despachado por'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->options([
                        'borrador' => 'Borrador',
                        'despachado' => 'Enviado',
                        'en_disputa' => 'En Disputa',
                        'completado' => 'Completado',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                // Acción de Despachar
                Tables\Actions\Action::make('despachar')
                    ->label('Enviar')
                    ->icon('heroicon-o-truck')
                    ->color('warning')
                    ->visible(fn($record) => $record->estado === 'borrador')
                    ->action(function ($record) {
                        app(\App\Services\TransferenciaService::class)->despachar($record);
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Confirmar Envío')
                    ->modalDescription('Al confirmar, se descontará el stock de la bodega de origen.'),

                // Acción de Recibir
                Tables\Actions\Action::make('recibir')
                    ->label('Recibir')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn($record) => $record->estado === 'despachado' && (auth()->user()->active_bodega_id == $record->bodega_destino_id || auth()->user()->hasRole('super_admin')))
                    ->form(function ($record) {
                        $fields = [];
                        foreach ($record->detalles as $detalle) {
                            $nombreItem = $detalle->article_id ? $detalle->article->nombre : $detalle->insumo->nombre;
                            $fields[] = Forms\Components\Section::make($nombreItem)
                                ->schema([
                                    Forms\Components\TextInput::make("detalle_{$detalle->id}")
                                        ->label('Cantidad Recibida')
                                        ->numeric()
                                        ->default($detalle->cantidad_enviada)
                                        ->required(),
                                ]);
                        }

                        return array_merge($fields, [
                            Forms\Components\FileUpload::make('evidencia_recepcion')
                                ->label('Fotos de Recepción (Evidencia)')
                                ->multiple()
                                ->image()
                                ->directory('transferencias/recepcion'),
                            Forms\Components\Textarea::make('observaciones_recepcion')
                                ->label('Observaciones'),
                            Forms\Components\Toggle::make('conforme')
                                ->label('¿Recibo todo conforme y sin daños?')
                                ->default(true),
                        ]);
                    })
                    ->action(function ($record, array $data) {
                        $detalles = [];
                        foreach ($data as $key => $value) {
                            if (str_starts_with($key, 'detalle_')) {
                                $id = str_replace('detalle_', '', $key);
                                $detalles[$id] = $value;
                            }
                        }

                        app(\App\Services\TransferenciaService::class)->recibir($record, [
                            'detalles' => $detalles,
                            'evidencia' => $data['evidencia_recepcion'],
                            'observaciones' => $data['observaciones_recepcion'],
                        ], $data['conforme']);
                    }),

                // Acción de Aprobar Disputa (Solo Admin)
                Tables\Actions\Action::make('aprobarDisputa')
                    ->label('Aprobar Disputa')
                    ->icon('heroicon-o-shield-check')
                    ->color('success')
                    ->visible(function ($record) {
                        /** @var \App\Models\User $user */
                        $user = \Illuminate\Support\Facades\Auth::user();
                        return $record->estado === 'en_disputa' && $user && $user->hasRole('admin');
                    })
                    ->action(function ($record) {
                        app(\App\Services\TransferenciaService::class)->aprobarDisputa($record);
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Aprobar Transferencia con Diferencias')
                    ->modalDescription('Al aprobar, se sumarán las cantidades reportadas como recibidas a la bodega destino.'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if (!$user) {
            return $query;
        }

        // Si es Bodega Principal (ID 1) o no tiene bodega activa (y es admin/super), ve todo.
        // Asumimos que ID 1 es Principal.
        if ($user->active_bodega_id && $user->active_bodega_id != 1) {
            $query->where(function (Builder $q) use ($user) {
                $q->where('bodega_origen_id', $user->active_bodega_id)
                    ->orWhere('bodega_destino_id', $user->active_bodega_id);
            });
        }

        return $query;
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
            'index' => Pages\ListTransferencias::route('/'),
            'create' => Pages\CreateTransferencia::route('/create'),
            'view' => Pages\ViewTransferencia::route('/{record}'),
            'edit' => Pages\EditTransferencia::route('/{record}/edit'),
        ];
    }
}

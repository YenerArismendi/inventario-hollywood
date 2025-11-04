<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    public static function getNavigationGroup(): ?string
    {
        return 'Gestión de acceso';
    }

    public static function getModelLabel(): string
    {
        return 'usuario';
    }

    protected static ?string $navigationIcon = 'heroicon-o-identification';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('tipo_documento')
                    ->label('Tipo de documento')
                    ->options([
                        'CC' => 'Cédula de ciudadanía',
                        'TI' => 'Tarjeta de identidad',
                        'CE' => 'Cédula de extranjería',
                        'NIT' => 'Número de Identificación Tributaria',
                        'PA' => 'Pasaporte',
                        'PEP' => 'Permiso Especial de Permanencia',
                        'RC' => 'Registro civil',
                        'NUIP' => 'Número Único de Identificación Personal',
                        'MS' => 'Menor sin identificación',
                    ])
                    ->required()
                    ->searchable(),
                Forms\Components\TextInput::make('documento_identidad')->required()
                    ->label('Número de documento'),
                Forms\Components\TextInput::make('name')->required()
                    ->label('Nombre completo')
                    ->afterStateUpdated(fn($state, callable $set) => $set('name', strtolower($state))),
                Forms\Components\TextInput::make('email')->required()
                    ->label('Correo electronico')
                    ->afterStateUpdated(fn($state, callable $set) => $set('email', strtolower($state)))
                    ->email(),
                Forms\Components\TextInput::make('telefono')
                    ->label('Número de telefono'),
                Forms\Components\TextInput::make('ciudad')->required()
                    ->afterStateUpdated(fn($state, callable $set) => $set('ciudad', strtolower($state)))
                    ->label('Cidudad'),
                Forms\Components\TextInput::make('direccion')
                    ->afterStateUpdated(fn($state, callable $set) => $set('direccion', strtolower($state)))
                    ->label('Dirección'),
                DatePicker::make('fecha_nacimiento')
                    ->label('Fecha de nacimiento')
                    ->displayFormat('d/m/Y')   // Formato que se muestra en el frontend
                    ->native(false)
                    ->closeOnDateSelection()   // Cierra automáticamente el selector al elegir una fecha
                    ->maxDate(now())           // No permite fechas futuras
                    ->required(),
                Select::make('genero')
                    ->label('Género')
                    ->options([
                        'Masculino' => 'Masculino',
                        'Femenino' => 'Femenino',
                        'Otro' => 'Otro',
                    ])
                    ->required()
                    ->native(false),
                Select::make('cargo')
                    ->label('Cargo')
                    ->options([
                        'administrador' => 'Administrador',
                        'supervisor_bodega' => 'Supervisor de Bodega',
                        'vendedor' => 'Vendedor',
                        'transportista' => 'Transportista',
                    ])
                    ->required()
                    ->native(false),
                Forms\Components\TextInput::make('password')
                    ->label('Contraseña')
                    ->password()
                    ->revealable() // 👁️ permite mostrar/ocultar la contraseña
                    ->dehydrateStateUsing(function ($state, $record) {
                        // Si el campo está vacío al editar, no cambia la contraseña
                        return filled($state) ? bcrypt($state) : $record->password;
                    })
                    ->helperText('Déjalo en blanco si no deseas cambiar la contraseña.')
                    ->required(fn(string $context): bool => $context === 'create'),
                Select::make('estado')
                    ->label('Estado')
                    ->options([
                        1 => 'Activo',
                        0 => 'Inactivo',
                    ])
                    ->required()
                    ->native(false)
                    ->default(1), // Opcional: estado por defecto
                Select::make('roles')
                    ->relationship('roles', 'name')
                    ->label('Roles (Grupos de acceso)')
                    ->multiple()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('bodegas')
                    ->label('Bodegas asignadas')
                    ->relationship('bodegas', 'nombre') // Relación definida en el modelo User
                    ->multiple()
                    ->preload()
                    ->searchable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tipo_documento')
                    ->label('Tipo Doc')
                    ->limit(20)
                    ->tooltip(fn($record) => $record->tipo_documento),

                Tables\Columns\TextColumn::make('documento_identidad')
                    ->label('Documento de identidad')
                    ->limit(20)
                    ->tooltip(fn($record) => $record->documento_identidad),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre completo')
                    ->limit(20)
                    ->tooltip(fn($record) => $record->name),

                Tables\Columns\TextColumn::make('email')
                    ->label('Correo electrónico')
                    ->limit(20)
                    ->tooltip(fn($record) => $record->email),

                Tables\Columns\TextColumn::make('telefono')
                    ->label('Teléfono')
                    ->limit(15)
                    ->tooltip(fn($record) => $record->telefono),

                Tables\Columns\TextColumn::make('ciudad')
                    ->label('Ciudad')
                    ->limit(20)
                    ->tooltip(fn($record) => $record->ciudad),

                Tables\Columns\TextColumn::make('direccion')
                    ->label('Dirección')
                    ->limit(20)
                    ->tooltip(fn($record) => $record->direccion),

                Tables\Columns\TextColumn::make('fecha_nacimiento')
                    ->label('Fecha de nacimiento'),

                Tables\Columns\TextColumn::make('genero')
                    ->label('Género'),

                Tables\Columns\TextColumn::make('cargo')
                    ->label('Cargo')
                    ->limit(20)
                    ->tooltip(fn($record) => $record->cargo),

                Tables\Columns\TextColumn::make('estado')
                    ->label('Estado'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha de creación')
                    ->dateTime(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use App\Helpers\PermissionHelper;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';

    public static function getNavigationGroup(): ?string
    {
        return 'Gestión de acceso';
    }

    public static function getModelLabel(): string
    {
        return 'Roles';
    }

    public static function form(Form $form): Form
    {
        // Agrupamos permisos por prefijo (ej: 'users.create' => 'users')
        $groupedPermissions = Permission::all()->groupBy(fn($p) => explode('.', $p->name)[0]);

        // Creamos array para el Select con optgroups
        $options = [];
        foreach ($groupedPermissions as $group => $permissions) {
            $options[ucfirst($group)] = $permissions->pluck('name', 'id')
                ->map(fn($name) => PermissionHelper::traducir($name))
                ->toArray();
        }

        return $form->schema([
            TextInput::make('name')
                ->label('Nombre del rol')
                ->required()
                ->maxLength(255),

            Select::make('permissions')
                ->label('Permisos')
                ->multiple()
                ->searchable()
                ->relationship('permissions', 'name') // Esto carga los permisos ya asignados
                ->options($options)
                ->required()
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre del rol')
                    ->searchable()
                    ->sortable(),
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
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}

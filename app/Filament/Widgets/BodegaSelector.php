<?php

namespace App\Filament\Widgets;

use Filament\Forms;
use Filament\Widgets\Widget;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use App\Models\Bodega;

class BodegaSelector extends Widget implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.widgets.bodega-selector-widget';

    public ?int $bodega_id = null;

    public static function canView(): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false; // No mostrar si no está autenticado
        }

        // Mostrar solo si el usuario tiene más de una bodega asociada
        return $user->bodegas()->count() > 1;
    }

    public function mount(): void
    {
        // Cargar la bodega activa actual del usuario
        $this->bodega_id = auth()->user()->active_bodega_id;
    }

    public function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('bodega_id')
                    ->label('Seleccionar Bodega')
                    ->options(
                        Bodega::whereHas('users', function ($query) {
                            $query->where('user_id', auth()->id());
                        })->pluck('nombre', 'id')
                    )
                    ->searchable()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state) {
                        $user = auth()->user();

                        if ($user) {
                            // Guardar automáticamente la bodega seleccionada en la tabla users
                            $user->update(['active_bodega_id' => $state]);

                            // Forzar que Filament/Livewire refresque los datos dependientes (como artículos)
                            $this->dispatch('refresh');
                        }
                    }),
            ]);
    }
}

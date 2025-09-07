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

    // Variable que guarda la bodega actualmente seleccionada en el widget
    public ?int $bodega_id = null;

    /**
     * Decide si el widget se muestra.
     * Solo se muestra si el usuario tiene más de una bodega asociada.
     */
    public static function canView(): bool
    {
        $user = auth()->user();

        if (!$user) {
            return false;
        }

        return $user->bodegas()->count() > 1;
    }

    /**
     * Se ejecuta al montar el widget.
     * Valida la bodega activa del usuario y la asigna automáticamente si es necesario.
     */
    public function mount(): void
    {
        $user = auth()->user();

        // Obtener IDs de las bodegas asociadas al usuario a través de la relación
        $bodegaIds = $user->bodegas()->pluck('bodegas.id');

        // Si la bodega activa no es válida o es nula
        if (!$user->active_bodega_id || ! $bodegaIds->contains($user->active_bodega_id)) {
            if ($bodegaIds->count() > 0) {
                // Asignar la primera bodega que encuentre
                $user->active_bodega_id = $bodegaIds->first();
            } else {
                // Si no tiene ninguna bodega, dejar nulo
                $user->active_bodega_id = null;
            }
            $user->save();
        }

        // Asignar la bodega activa al widget
        $this->bodega_id = $user->active_bodega_id;
    }

    /**
     * Define el formulario del widget (selector de bodega)
     */
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
                        if ($user && $user->bodegas()->pluck('bodegas.id')->contains($state)) {
                            $user->active_bodega_id = $state;
                            $user->save();
                            $this->dispatch('refresh');
                        }
                    })
            ]);
    }
}

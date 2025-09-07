<?php

namespace App\Livewire\Topbar;

use Livewire\Component;
use App\Models\Bodega;

class BodegaSelector extends Component
{
    public ?int $bodega_id = null;
    public array $bodegas = [];

    public function mount()
    {
        $user = auth()->user();
        if (!$user) return;

        $this->bodegas = $user->bodegas()
            ->select('bodegas.id', 'bodegas.nombre')
            ->pluck('nombre','id')
            ->toArray();

        $this->bodega_id = $user->active_bodega_id ?? array_key_first($this->bodegas);
    }

    // Método normal, no un lifecycle hook
    public function changeBodega($value)
    {
        $user = auth()->user();
        if (!$user) return;

        if (in_array($value, array_keys($this->bodegas))) {
            $this->bodega_id = $value;
            $user->active_bodega_id = $value;
            $user->save();

            // Emitir evento para widgets
            $this->dispatch('bodegaChanged', $value);
        }
    }

    public function render()
    {
        return view('livewire.topbar.bodega-selector');
    }
}

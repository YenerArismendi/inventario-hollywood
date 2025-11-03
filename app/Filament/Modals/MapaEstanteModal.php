<?php

namespace App\Filament\Modals;

use Filament\Forms\Components\ModalComponent;
use App\Models\Estante;

class MapaEstanteModal extends ModalComponent
{
    public int $estante_id;

    public function mount(int $estante_id)
    {
        $this->estante_id = $estante_id;
    }

    public function render()
    {
        $estante = Estante::with('ubicaciones.producto')->find($this->estante_id);
        return view('filament.modals.mapa-estante', compact('estante'));
    }
}

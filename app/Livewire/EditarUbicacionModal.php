<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Ubicacion;
use App\Models\Article;

class EditarUbicacionModal extends Component
{
    public $ubicacionId;
    public $articles_id; // 👈 este es el campo real en la BD
    public $productos = [];

    protected $listeners = ['abrirModalUbicacion' => 'cargarUbicacion'];

    public function mount()
    {
        // Carga la lista de artículos disponibles
        $this->productos = Article::orderBy('nombre')->get();
    }

    public function cargarUbicacion($ubicacionId)
    {
        $this->ubicacionId = $ubicacionId;

        $ubicacion = Ubicacion::find($ubicacionId);
        if ($ubicacion) {
            $this->articles_id = $ubicacion->articles_id;
        }

        // Vuelve a cargar los productos por seguridad
        $this->productos = Article::orderBy('nombre')->get();

        // Abre el modal Filament
        $this->dispatch('open-modal', id: 'editar-ubicacion');
    }

    public function guardar()
    {
        // 🧪 Validación rápida: asegúrate que llega aquí
        // dd('✅ Entró al método guardar', [
        //     'ubicacionId' => $this->ubicacionId,
        //     'articles_id' => $this->articles_id,
        // ]);

        $ubicacion = Ubicacion::find($this->ubicacionId);

        if ($ubicacion) {
            $ubicacion->articles_id = $this->articles_id;
            $ubicacion->save();

            session()->flash('success', 'Producto asignado correctamente.');

            // Cierra el modal
            $this->dispatch('close-modal', id: 'editar-ubicacion');

            // Refresca el grid
            $this->dispatch('ubicacion-actualizada');
        }
    }

    public function render()
    {
        return view('livewire.editar-ubicacion-modal');
    }
}

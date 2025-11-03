<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Bodega;

class MapaBodega extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-map';
    protected static string $view = 'filament.pages.mapa-bodega';
    protected static ?string $navigationLabel = 'Mapa de Bodega';

    public ?Bodega $bodega = null;

    public function mount(): void
    {
        // Cargar la primera bodega solo como ejemplo
        $this->bodega = Bodega::with('estantes.ubicaciones.producto')->first();
    }
}
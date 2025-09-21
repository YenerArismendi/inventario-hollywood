<?php

namespace App\Providers;

use Filament\View\PanelsRenderHook;
use Illuminate\Contracts\View\View;
use Filament\Support\Facades\FilamentView;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use App\Models\Bodega;
use App\Models\MovimientoInventario;
use App\Observers\BodegaObserver;
use App\Observers\MovimientoInventarioObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Inyectar el dropdown en el topbar de Filament
        //Apartado para mostrar el menu para cambiar de bodega para el usuario
        FilamentView::registerRenderHook(
            PanelsRenderHook::TOPBAR_START,
            fn (): string => Blade::render('@livewire(\'topbar.bodega-selector\')')
        );

        Bodega::observe(BodegaObserver::class);
        MovimientoInventario::observe(MovimientoInventarioObserver::class);
    }
}

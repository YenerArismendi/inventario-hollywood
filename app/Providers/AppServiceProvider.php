<?php

namespace App\Providers;

use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
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
        FilamentView::registerRenderHook(
            PanelsRenderHook::TOPBAR_START,
            fn (): string => Blade::render('@livewire(\'topbar.bodega-selector\')')
        );

        Bodega::observe(BodegaObserver::class);
        MovimientoInventario::observe(MovimientoInventarioObserver::class);
    }
}

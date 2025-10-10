<?php

namespace App\Providers;

use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Filament\Support\Facades\FilamentView;
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
        // ✅ Forzar HTTPS en Railway o producción
        if (env('APP_ENV') === 'production' || env('FORCE_HTTPS', false)) {
            URL::forceScheme('https');
        }

        // Inyectar el dropdown en el topbar de Filament
        FilamentView::registerRenderHook(
            PanelsRenderHook::TOPBAR_START,
            fn (): string => Blade::render('@livewire("topbar.bodega-selector")')
        );

        // Observadores de modelos
        Bodega::observe(BodegaObserver::class);
        MovimientoInventario::observe(MovimientoInventarioObserver::class);
    }
}

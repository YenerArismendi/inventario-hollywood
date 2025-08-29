<?php

namespace App\Providers;

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
        Bodega::observe(BodegaObserver::class);
        MovimientoInventario::observe(MovimientoInventarioObserver::class);
    }
}

<?php

namespace App\Providers;

use App\Models\Suppliers;
use App\Models\User;
use App\Policies\SuppliersPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Permission;
use App\Policies\PermissionPolicy;
use App\Models\{Bodega, Caja, Cliente, SesionCaja, Venta};
use App\Policies\BodegaPolicy;
use App\Policies\CajaPolicy;
use App\Policies\ClientePolicy;
use App\Policies\SesionCajaPolicy;
use App\Policies\VentaPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Permission::class => PermissionPolicy::class,
        Suppliers::class => SuppliersPolicy::class,
        Bodega::class => BodegaPolicy::class,
        Cliente::class => ClientePolicy::class,
        Caja::class => CajaPolicy::class,
        SesionCaja::class => SesionCajaPolicy::class,
        Venta::class => VentaPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}

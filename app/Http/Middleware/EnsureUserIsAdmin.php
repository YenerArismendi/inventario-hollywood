<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // No aplicar en las rutas públicas del panel de Filament
        if ($request->routeIs(
            'filament.admin.auth.login',
            'filament.admin.auth.logout',
            'filament.admin.auth.password.*'
        )) {
            return $next($request);
        }

        // Verifica autenticación y rol del usuario
        if (!auth()->check() || !auth()->user()->hasRole('admin')) {
            abort(403, 'Acceso no autorizado');
        }

        return $next($request);
    }
}

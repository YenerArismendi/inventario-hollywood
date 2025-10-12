<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserActive
{
    public function handle(Request $request, Closure $next): Response
    {
        // Evita aplicar este middleware en rutas públicas (login, logout, password reset)
        if ($request->routeIs(
            'filament.admin.auth.login',
            'filament.admin.auth.logout',
            'filament.admin.auth.password.*'
        )) {
            return $next($request);
        }

        // Verifica si el usuario está autenticado y su estado
        if (auth()->check() && (int) auth()->user()->estado !== 1) {
            auth()->logout();

            return redirect()
                ->route('filament.admin.auth.login')
                ->with('inactive_message', 'Tu cuenta está inactiva, contacta al administrador.');
        }

        return $next($request);
    }
}

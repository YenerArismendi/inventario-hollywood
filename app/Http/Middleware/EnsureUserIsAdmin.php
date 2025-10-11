<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // Verifica si hay usuario autenticado y tiene rol admin
        if (!auth()->check() || !auth()->user()->hasRole('admin')) {
            abort(403, 'Acceso no autorizado');
        }

        return $next($request);
    }
}

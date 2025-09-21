<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserActive
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && (int) auth()->user()->estado !== 1) {
            auth()->logout();

            return redirect()
                ->route('filament.admin.auth.login') // 👈 usa tu panel aquí
                ->with('inactive_message', 'Tu cuenta está inactiva, contacta al administrador.');
        }

        return $next($request);
    }
}

<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Login as BaseLogin;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Notifications\Notification;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    /**
     * Se ejecuta al montar la página.
     * Aquí mostramos el mensaje si el middleware expulsó al usuario.
     */
    public function mount(): void
    {
        parent::mount();

        if (session()->has('inactive_message')) {
            Notification::make()
                ->title('Acceso denegado')
                ->body(session('inactive_message'))
                ->danger()
                ->send();
        }
    }

    /**
     * Autenticación al iniciar sesión.
     */
    public function authenticate(): ?LoginResponse
    {
        $response = parent::authenticate();

        if ((int) auth()->user()->estado !== 1) {
            auth()->logout();

            Notification::make()
                ->title('Acceso denegado')
                ->body('Tu cuenta está inactiva, contacta al administrador.')
                ->danger()
                ->send();

            return app(LoginResponse::class);
        }

        return $response;
    }
}

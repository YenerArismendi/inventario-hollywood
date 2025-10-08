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
        try {
            $response = parent::authenticate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Si las credenciales son inválidas, Filament se encarga de mostrar el error.
            // Simplemente relanzamos la excepción para no interferir con ese flujo.
            throw $e;
        }

        // Esta comprobación solo se ejecutará si la autenticación fue exitosa.
        if (auth()->check() && (int)auth()->user()->estado !== 1) {
            $user = auth()->user(); // Guardamos el usuario antes de hacer logout
            auth()->logout();

            // Lanzamos una excepción de validación para mostrar el mensaje de error en el formulario.
            throw \Illuminate\Validation\ValidationException::withMessages([
                'data.email' => 'Tu cuenta está inactiva, contacta al administrador.',
            ]);
        }

        return $response;
    }
}

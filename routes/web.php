<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

Route::get('/', function () {
    return view('welcome');
});

/**
 * Ruta para gestionar el inicio de sesion de un usuario
 */

Route::post('/login', function (Request $request) {
    $credentials = $request->only('email', 'password');
    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return response()->json(Auth::user());
    }
    return response()->json(['message' => 'Credenciales inválidas'], 401);
});

/**
 * Cierra la sesión del usuario autenticado.
 * Esta ruta debe ser protegida para que solo usuarios logueados puedan acceder a ella.
 */
Route::post('/logout', function (Request $request) {
    // Invalida la sesión del usuario en el servidor.
    Auth::logout();

    // Invalida todos los datos de la sesión actual.
    $request->session()->invalidate();

    // Regenera el token CSRF para la siguiente sesión.
    $request->session()->regenerateToken();

    // Devuelve una respuesta exitosa.
    return response()->json(['message' => 'Sesión cerrada correctamente.']);
})->middleware('auth');

Route::get('/crear-admin', function () {
    $role = Role::firstOrCreate(['name' => 'admin']);

    $user = User::firstOrCreate(
        ['email' => 'admin@tuapp.com'],
        [
            'name' => 'Administrador',
            'password' => Hash::make('12345678'),
            'estado' => 1,
        ]
    );

    $user->assignRole($role);

    return 'Usuario admin creado correctamente.';
});

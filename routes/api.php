<?php
// routes/api.php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\ArticleController;

// Agrupamos por versión
Route::prefix('v1')->group(function () {
    Route::get('/articles', [ArticleController::class, 'index']);
    // Aquí irían más rutas:
    // Route::post('/ventas', [VentaController::class, 'store']);
    // Route::get('/caja/sesion-activa', [SesionCajaController::class, 'showActive']);
});

// Esta ruta es para obtener el usuario autenticado
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

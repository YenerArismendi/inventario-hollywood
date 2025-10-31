<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\ArticleController;
use App\Http\Controllers\Api\V1\CajaController;
use App\Http\Controllers\Api\V1\VentaController;
use App\Http\Controllers\Api\V1\ClienteController;
use App\Http\Controllers\Api\V1\SesionCajaController;

// Esta ruta es para obtener el usuario autenticado
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Agrupamos todas las rutas de la API v1 y las protegemos con Sanctum.
Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    // Rutas de Artículos
    Route::get('/articles', [ArticleController::class, 'index']);
    // Rutas para Cajas y Sesiones
    Route::get('/cajas-disponibles', [CajaController::class, 'index']);
    Route::get('/sesiones-caja', [SesionCajaController::class, 'index']);
    Route::get('/sesion-caja/activa', [SesionCajaController::class, 'showActive']);
    Route::post('/sesion-caja/abrir', [SesionCajaController::class, 'store']);
    Route::post('/sesion-caja/{sesion}/cerrar', [SesionCajaController::class, 'close']);
    // Rutas para Ventas
    Route::post('/ventas', [VentaController::class, 'store']);
    Route::get('/ventas', [VentaController::class, 'index']);
    Route::get('/ventas/sesion-actual', [VentaController::class, 'currentSessionSales']);
    // Rutas para Clientes
    Route::get('/clientes', [ClienteController::class, 'index']);
});


<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $sesionActiva = $user->sesionCajaActiva;

        // Si no hay sesión de caja activa, no podemos saber de qué bodega mostrar artículos.
        if (!$sesionActiva) {
            // Devolvemos una colección vacía con un error o mensaje claro.
            return ArticleResource::collection([]);
        }

        // Obtenemos la bodega desde la sesión de caja activa.
        $bodega = $sesionActiva->caja->bodega;

        // Obtenemos los artículos de esa bodega con su stock (pivot)
        $articles = $bodega->articles()->where('estado', '1')->wherePivot('stock', '>', 0)->get();

        // Usamos el Resource Collection para transformar la lista de artículos
        return ArticleResource::collection($articles);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

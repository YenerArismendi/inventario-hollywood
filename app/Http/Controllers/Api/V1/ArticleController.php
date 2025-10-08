<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Models\Bodega;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Asumimos que la bodega principal para ventas tiene ID 1
        // Es mejor obtener esto de una configuración o de forma más dinámica
        $bodegaPrincipalId = 1;

        $bodega = Bodega::findOrFail($bodegaPrincipalId);

        // Obtenemos los artículos de esa bodega con su stock (pivot)
        $articles = $bodega->articles()->where('estado', '1')->get();

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

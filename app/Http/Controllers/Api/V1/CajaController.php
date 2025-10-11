<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CajaResource;
use App\Models\Caja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CajaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Si el usuario es admin, muestra todas las cajas activas.
        if ($user->hasRole('admin')) {
            $cajas = Caja::where('activa', true)->get();
            return CajaResource::collection($cajas);
        }

        // Para otros usuarios, muestra solo las cajas activas de sus bodegas asignadas.
        $bodegaIds = $user->bodegas()->pluck('bodegas.id');
        $cajas = Caja::whereIn('bodega_id', $bodegaIds)
            ->where('activa', true)
            ->get();

        return CajaResource::collection($cajas);
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

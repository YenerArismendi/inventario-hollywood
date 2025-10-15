<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClienteResource;
use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    /**
     * Muestra una lista de clientes, con la opción de filtrar por aquellos que tienen crédito.
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $clientes = Cliente::query()
            ->when($request->boolean('con_credito'), function ($query) {
                $query->where('tiene_credito', true);
            })
            ->get();

        return ClienteResource::collection($clientes);
    }
}

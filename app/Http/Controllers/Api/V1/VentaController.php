<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\VentaResource;
use App\Models\Venta;
use App\Services\VentaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VentaController extends Controller
{
    public function __construct(protected VentaService $ventaService)
    {
    }

    /**
     * Muestra el historial paginado de ventas del usuario autenticado.
     */
    public function index()
    {
        $user = Auth::user();

        $ventas = Venta::where('user_id', $user->id)
            ->with(['detalles.article', 'bodega']) // Precargamos relaciones para optimizar
            ->latest() // Ordenamos por fecha, de la más reciente a la más antigua
            ->paginate(20); // Paginamos los resultados

        return VentaResource::collection($ventas);
    }

    /**
     * Muestra las ventas realizadas durante la sesión de caja activa del usuario.
     */
    public function currentSessionSales()
    {
        $user = Auth::user();
        $sesionActiva = $user->sesionCajaActiva;

        // Si no hay sesión activa, devolvemos una colección vacía.
        if (!$sesionActiva) {
            return VentaResource::collection([]);
        }

        $ventas = Venta::where('sesion_caja_id', $sesionActiva->id)
            ->with(['detalles.article', 'bodega'])
            ->latest()
            ->get();

        return VentaResource::collection($ventas);
    }

    /**
     * Almacena una nueva venta.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cliente_id' => 'nullable|integer|exists:clientes,id',
            'metodo_pago' => 'required|string|in:efectivo,tarjeta,transferencia',
            'subtotal' => 'required|numeric',
            'total' => 'required|numeric',
            'descuento' => 'nullable|numeric',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|integer|exists:articles,id',
            'items.*.cantidad' => 'required|integer|min:1',
            'items.*.precio_venta' => 'required|numeric',
        ]);

        $venta = $this->ventaService->crearVenta(Auth::user(), $validated);

        // Devolvemos la venta creada con todos sus detalles.
        return new VentaResource($venta->load('detalles'));
    }
}

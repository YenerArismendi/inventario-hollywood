<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\SesionCajaResource;
use App\Models\SesionCaja;
use App\Services\SesionCajaService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class SesionCajaController extends Controller
{
    // Inyectamos nuestro servicio en el constructor para tenerlo disponible en todos los métodos.
    public function __construct(protected SesionCajaService $sesionCajaService)
    {
    }

    /**
     * Muestra el historial de sesiones de caja del usuario autenticado.
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $sesiones = $user->sesionesCaja()
            ->with(['caja', 'aprobadoPor']) // Precargamos relaciones para optimizar
            ->latest('fecha_apertura') // Ordenamos de más reciente a más antigua
            ->paginate(15); // Paginamos para no sobrecargar el frontend

        return SesionCajaResource::collection($sesiones);
    }

    /**
     * Muestra la sesión de caja activa del usuario autenticado.
     */
    public function showActive()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $sesionActiva = $user->sesionCajaActiva;

        if (!$sesionActiva) {
            // Usamos el código 204 No Content para indicar que no hay una sesión activa.
            return response()->noContent();
        }

        return new SesionCajaResource($sesionActiva);
    }

    /**
     * Abre una nueva sesión de caja.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'caja_id' => 'required|integer|exists:cajas,id',
            'monto_inicial' => 'required|numeric|min:0',
        ]);

        $sesion = $this->sesionCajaService->abrirSesion(
            $validated['caja_id'],
            $validated['monto_inicial'],
            Auth::id()
        );

        return new SesionCajaResource($sesion);
    }

    /**
     * Cierra la sesión de caja especificada.
     */
    public function close(Request $request, SesionCaja $sesion)
    {
        // Verificamos que el usuario solo pueda cerrar su propia sesión.
        if ($sesion->user_id !== Auth::id()) {
            abort(403, 'No autorizado para cerrar esta sesión.');
        }

        $validated = $request->validate([
            'monto_final_contado' => 'required|numeric|min:0',
            'notas_cierre' => 'nullable|string',
        ]);

        $this->sesionCajaService->cerrarSesion(
            $sesion,
            $validated['monto_final_contado'],
            $validated['notas_cierre']
        );

        return response()->json(['message' => 'La sesión ha sido enviada para aprobación.']);
    }
}

<?php

namespace App\Services;

use App\Models\Caja;
use App\Models\SesionCaja;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class SesionCajaService
{
    /**
     * Abre una nueva sesión de caja para un usuario.
     *
     * @param int $cajaId
     * @param float $montoInicial
     * @param int $userId
     * @return SesionCaja
     * @throws ValidationException
     */
    public function abrirSesion(int $cajaId, float $montoInicial, int $userId): SesionCaja
    {
        // Validación: ¿Ya existe una sesión abierta para esta caja?
        $sesionExistente = SesionCaja::where('caja_id', $cajaId)->where('estado', 'abierta')->exists();
        if ($sesionExistente) {
            throw ValidationException::withMessages([
                'caja_id' => 'Esta caja ya tiene una sesión abierta.',
            ]);
        }

        // Validación: ¿El usuario ya tiene una sesión abierta en otra caja?
        $usuarioTieneSesion = SesionCaja::where('user_id', $userId)->where('estado', 'abierta')->exists();
        if ($usuarioTieneSesion) {
            throw ValidationException::withMessages([
                'user_id' => 'Ya tienes una sesión de caja abierta en otro lugar.',
            ]);
        }

        return SesionCaja::create([
            'caja_id' => $cajaId,
            'user_id' => $userId,
            'monto_inicial' => $montoInicial,
            'fecha_apertura' => now(),
            'estado' => 'abierta',
        ]);
    }

    /**
     * Cierra una sesión de caja existente.
     *
     * @param SesionCaja $sesion
     * @param float $montoFinalContado
     * @param string|null $notasCierre
     * @return SesionCaja
     */
    public function cerrarSesion(SesionCaja $sesion, float $montoFinalContado, ?string $notasCierre): SesionCaja
    {
        // 1. Calculamos los totales de ventas por cada método de pago
        $ventas = $sesion->ventas();
        $totalEfectivo = $ventas->clone()->where('metodo_pago', 'efectivo')->sum('total');
        $totalTransferencia = $ventas->clone()->where('metodo_pago', 'transferencia')->sum('total');
        $totalCredito = $ventas->clone()->where('metodo_pago', 'credito')->sum('total');

        // 2. El dinero que DEBERÍA HABER en la caja es la base + solo las ventas en efectivo
        $montoEfectivoCalculado = $sesion->monto_inicial + $totalEfectivo;

        // 3. La diferencia se calcula contra lo que debería haber en efectivo
        $diferencia = $montoFinalContado - $montoEfectivoCalculado;

        $sesion->update([
            'total_ventas_efectivo' => $totalEfectivo,
            'total_ventas_transferencia' => $totalTransferencia,
            'total_ventas_credito' => $totalCredito,
            'monto_final_efectivo_calculado' => $montoEfectivoCalculado,
            'monto_final_contado' => $montoFinalContado,
            'diferencia' => $diferencia,
            'notas_cierre' => $notasCierre,
            'fecha_cierre' => now(),
            'estado' => 'pendiente_aprobacion',
        ]);

        return $sesion;
    }

    /**
     * Aprueba el cierre de una sesión de caja.
     *
     * @param SesionCaja $sesion
     * @return SesionCaja
     */
    public function aprobarCierre(SesionCaja $sesion): SesionCaja
    {
        $sesion->update([
            'estado' => 'aprobada',
            'aprobado_por_id' => Auth::id(),
        ]);

        return $sesion;
    }
}

<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Cliente;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class VentaService
{
    /**
     * Procesa y registra una nueva venta de forma transaccional.
     *
     * @param User $user El usuario que realiza la venta.
     * @param array $datosVenta Los datos de la venta provenientes del frontend.
     * @return Venta La venta creada.
     * @throws ValidationException
     */
    public function crearVenta(User $user, array $datosVenta): Venta
    {
        // Validar que el usuario tiene una sesión de caja activa.
        $sesionCaja = $user->sesionCajaActiva;
        if (!$sesionCaja) {
            throw ValidationException::withMessages([
                'sesion_caja' => 'No tienes una sesión de caja activa para registrar ventas.',
            ]);
        }

        // Validación específica para ventas a crédito
        if ($datosVenta['metodo_pago'] === 'credito') {
            if (empty($datosVenta['cliente_id'])) {
                throw ValidationException::withMessages(['cliente_id' => 'Se debe seleccionar un cliente para ventas a crédito.']);
            }

            $cliente = Cliente::findOrFail($datosVenta['cliente_id']);

            if (!$cliente->tiene_credito) {
                throw ValidationException::withMessages(['cliente_id' => 'Este cliente no tiene el crédito habilitado.']);
            }

            if ($cliente->en_mora) {
                throw ValidationException::withMessages(['cliente_id' => 'El cliente se encuentra en mora y no puede realizar compras a crédito.']);
            }

            if ($cliente->credito_disponible < $datosVenta['total']) {
                throw ValidationException::withMessages(['total' => "El cliente no tiene crédito suficiente. Disponible: {$cliente->credito_disponible}"]);
            }
        }

        // Usamos una transacción para asegurar la integridad de los datos.
        return DB::transaction(function () use ($user, $sesionCaja, $datosVenta) {

            // Validar stock y bloquear los artículos para evitar concurrencia.
            $bodega = $sesionCaja->caja->bodega;
            foreach ($datosVenta['items'] as $item) {
                $articulo = $bodega->articles()->where('article_id', $item['id'])->lockForUpdate()->first();

                if (!$articulo || $articulo->pivot->stock < $item['cantidad']) {
                    $nombreArticulo = Article::find($item['id'])->nombre ?? 'ID ' . $item['id'];
                    throw ValidationException::withMessages([
                        'items' => "Stock insuficiente para el artículo: {$nombreArticulo}. Disponible: " . ($articulo->pivot->stock ?? 0),
                    ]);
                }
            }

            // Crear el registro principal de la Venta.
            $venta = Venta::create([
                'user_id' => $user->id,
                'bodega_id' => $bodega->id,
                'sesion_caja_id' => $sesionCaja->id,
                'cliente_id' => $datosVenta['cliente_id'] ?? null,
                'subtotal' => $datosVenta['subtotal'],
                'descuento' => $datosVenta['descuento'] ?? 0,
                'total' => $datosVenta['total'],
                'metodo_pago' => $datosVenta['metodo_pago'],
                'estado' => 'completada',
            ]);

            // 4. Crear los detalles de la venta y descontar el stock.
            foreach ($datosVenta['items'] as $item) {
                $venta->detalles()->create([
                    'article_id' => $item['id'],
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio_venta'],
                    'subtotal_item' => $item['cantidad'] * $item['precio_venta'],
                ]);

                // Descontamos el stock del artículo en la bodega.
                $bodega->articles()->updateExistingPivot($item['id'], [
                    'stock' => DB::raw("stock - {$item['cantidad']}")
                ]);
            }

            // 5. Si la venta es a crédito, actualizamos la deuda del cliente.
            if ($venta->metodo_pago === 'credito') {
                $venta->cliente->increment('deuda_actual', $venta->total);
            }

            return $venta;
        });
    }
}

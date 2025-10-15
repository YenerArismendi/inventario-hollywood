<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SesionCajaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'monto_inicial' => $this->monto_inicial,
            'fecha_apertura' => $this->fecha_apertura->toIso8601String(),
            'estado' => $this->estado,
            'caja' => [
                'id' => $this->caja->id,
                'nombre' => $this->caja->nombre,
            ],
            'usuario' => [
                'id' => $this->user->id,
                'nombre' => $this->user->name,
            ],
            // Campos de cierre (solo se añaden a la respuesta si tienen valor)
            'total_ventas_efectivo' => $this->whenNotNull($this->total_ventas_efectivo),
            'total_ventas_transferencia' => $this->whenNotNull($this->total_ventas_transferencia),
            'total_ventas_credito' => $this->whenNotNull($this->total_ventas_credito),
            'monto_final_efectivo_calculado' => $this->whenNotNull($this->monto_final_efectivo_calculado),
            'monto_final_contado' => $this->whenNotNull($this->monto_final_contado),
            'diferencia' => $this->whenNotNull($this->diferencia),
            'fecha_cierre' => $this->whenNotNull($this->fecha_cierre?->toIso8601String()),
            'notas_cierre' => $this->whenNotNull($this->notas_cierre),
            'aprobado_por' => $this->whenLoaded('aprobadoPor', fn() => $this->aprobadoPor?->name),
        ];
    }
}

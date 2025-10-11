<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VentaResource extends JsonResource
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
            'fecha' => $this->created_at->toIso8601String(),
            'subtotal' => $this->subtotal,
            'descuento' => $this->descuento,
            'total' => $this->total,
            'metodo_pago' => $this->metodo_pago,
            'estado' => $this->estado,
            'usuario' => $this->usuario->name,
            'bodega' => $this->bodega->nombre,
            'cliente' => $this->whenLoaded('cliente', fn() => $this->cliente?->nombre_completo),
            'detalles' => VentaDetalleResource::collection($this->whenLoaded('detalles')),
        ];
    }
}

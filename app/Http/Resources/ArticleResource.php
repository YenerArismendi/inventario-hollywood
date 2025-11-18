<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
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
            'nombre' => $this->nombre,
            'codigo_barras' => $this->codigo_barras,
            'precio_venta' => $this->precio_venta,
            'unidad' => $this->unidad_medida,
            'imagen_url' => $this->imagen ? url('storage/' . $this->imagen) : null, // Generar URL completa
            // Cargar el stock solo si la relación está cargada
            'stock' => $this->whenPivotLoaded('bodega_article', function () {
                return $this->pivot->stock;
            }),
        ];
    }
}

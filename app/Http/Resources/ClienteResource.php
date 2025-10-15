<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClienteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'documento_identidad' => $this->documento_identidad,
            'tiene_credito' => $this->tiene_credito,
            'limite_credito' => $this->limite_credito,
            'deuda_actual' => $this->deuda_actual,
            'credito_disponible' => $this->credito_disponible,
            'en_mora' => $this->en_mora,
        ];
    }
}

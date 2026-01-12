<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transferencia extends Model
{
    protected $fillable = [
        'bodega_origen_id',
        'bodega_destino_id',
        'user_despacho_id',
        'user_recepcion_id',
        'estado',
        'evidencia_despacho',
        'evidencia_recepcion',
        'observaciones_despacho',
        'observaciones_recepcion',
        'fecha_despacho',
        'fecha_recepcion',
    ];

    protected $casts = [
        'evidencia_despacho' => 'array',
        'evidencia_recepcion' => 'array',
        'fecha_despacho' => 'datetime',
        'fecha_recepcion' => 'datetime',
    ];

    public function bodegaOrigen(): BelongsTo
    {
        return $this->belongsTo(Bodega::class, 'bodega_origen_id');
    }

    public function bodegaDestino(): BelongsTo
    {
        return $this->belongsTo(Bodega::class, 'bodega_destino_id');
    }

    public function userDespacho(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_despacho_id');
    }

    public function userRecepcion(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_recepcion_id');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(TransferenciaDetalle::class);
    }
}

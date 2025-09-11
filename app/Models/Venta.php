<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bodega_id',
        'cliente_id',
        'sesion_caja_id',
        'subtotal',
        'descuento',
        'total',
        'metodo_pago',
        'estado',
    ];

    public function detalles()
    {
        return $this->hasMany(VentaDetalle::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function sesionCaja()
    {
        return $this->belongsTo(SesionCaja::class);
    }

    public function bodega()
    {
        return $this->belongsTo(Bodega::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SesionCaja extends Model
{
    use HasFactory;

    protected $fillable = [
        'caja_id',
        'user_id',
        'monto_inicial',
        'monto_final_calculado',
        'monto_final_contado',
        'diferencia',
        'fecha_apertura',
        'fecha_cierre',
        'estado',
        'aprobado_por_id',
        'notas_cierre'
    ];

    protected $casts = [
        'fecha_apertura' => 'datetime',
        'fecha_cierre' => 'datetime',
        'monto_inicial' => 'decimal:2',
        'monto_final_calculado' => 'decimal:2',
        'monto_final_contado' => 'decimal:2',
        'diferencia' => 'decimal:2',
    ];

    public function caja()
    {
        return $this->belongsTo(Caja::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function aprobadoPor()
    {
        return $this->belongsTo(User::class, 'aprobado_por_id');
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }
}

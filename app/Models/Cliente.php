<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'tipo_documento',
        'documento_identidad',
        'telefono',
        'departamento',
        'ciudad',
        'direccion',
        'email',
        'fecha_nacimiento',
        'genero',
        'estado',
        'tiene_credito',
        'limite_credito',
        'dias_credito',
        'user_id',
        'deuda_actual',
    ];

    protected $casts = [
        'tiene_credito' => 'boolean',
        'limite_credito' => 'decimal:2',
        'deuda_actual' => 'decimal:2',
    ];

    /**
     * Atributo calculado: Crédito disponible.
     */
    protected function creditoDisponible(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->limite_credito - $this->deuda_actual
        );
    }

    /**
     * Atributo calculado: ¿Está en mora?
     */
    protected function enMora(): Attribute
    {
        return Attribute::make(
            get: function () {
                $ventaMasAntiguaSinPagar = $this->ventas()->where('metodo_pago', 'credito')->where('estado', 'completada')->orderBy('created_at', 'asc')->first();
                if (!$ventaMasAntiguaSinPagar) return false;

                return $ventaMasAntiguaSinPagar->created_at->addDays($this->dias_credito)->isPast();
            }
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }
}

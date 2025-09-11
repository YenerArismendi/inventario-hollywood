<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'tipo_documento',
        'documento_identidad',
        'telefono',
        'ciudad',
        'direccion',
        'email',
        'fecha_nacimiento',
        'genero',
        'estado',
        'tiene_credito',
        'limite_credito',
        'dias_credito',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }
}

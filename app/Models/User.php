<?php

namespace App\Models;

use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    use HasRoles, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'tipo_documento',
        'documento_identidad',
        'telefono',
        'ciudad',
        'direccion',
        'fecha_nacimiento',
        'genero',
        'cargo',
        'estado',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    /**
     * Obtiene la bodega asignada al usuario.
     */
    public function bodegas(): BelongsToMany
    {
        return $this->belongsToMany(Bodega::class, 'bodega_user');
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }

    public function sesionesCaja()
    {
        return $this->hasMany(SesionCaja::class);
    }

    public function sesionCajaActiva()
    {
        return $this->hasOne(SesionCaja::class)->where('estado', 'abierta');
    }

//    public function canAccessPanel(Panel $panel): bool
//    {
//        if ($this->estado !== '1') {
//            throw ValidationException::withMessages([
//                'email' => 'Tu cuenta está inactiva, por favor contacta al administrador.',
//            ]);
//        }
//
//        return true;
//    }
}

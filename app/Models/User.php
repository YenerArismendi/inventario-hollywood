<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
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
     * Define si el usuario puede acceder al panel de Filament (Filament v3).
     * @param \Filament\Panel $panel
     * @return bool
     */
    public function canAccessPanel(\Filament\Panel $panel): bool
    {
        // Devuelve 'true' si el usuario tiene el rol de 'admin'.
        // Permite acceso si el usuario tiene *cualquier* rol asignado
        return $this->roles()->exists();
    }

    /**
     * Obtiene la bodega asignada al usuario.
     */
    public function bodegas(): BelongsToMany
    {
        return $this->belongsToMany(Bodega::class, 'bodega_user');
    }

    public function ventas(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Venta::class);
    }

    public function sesionesCaja(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SesionCaja::class);
    }

    public function sesionCajaActiva(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(SesionCaja::class)->where('estado', 'abierta');
    }
}

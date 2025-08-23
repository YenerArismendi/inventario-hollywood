<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

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

    // Aquí es donde agregas la lógica para convertir en minúscula
    protected static function booted()
    {
        static::creating(function ($user) {
            $user->name = strtolower($user->name);
            $user->email = strtolower($user->email);
            $user->tipo_documento = strtolower($user->tipo_documento);
            $user->documento_identidad = strtolower($user->documento_identidad);
            $user->telefono = strtolower($user->telefono);
            $user->ciudad = strtolower($user->ciudad);
            $user->direccion = strtolower($user->direccion);
            $user->genero = strtolower($user->genero);
            $user->cargo = strtolower($user->cargo);
            $user->estado = strtolower($user->estado);
        });

        static::updating(function ($user) {
            $user->name = strtolower($user->name);
            $user->email = strtolower($user->email);
            $user->tipo_documento = strtolower($user->tipo_documento);
            $user->documento_identidad = strtolower($user->documento_identidad);
            $user->telefono = strtolower($user->telefono);
            $user->ciudad = strtolower($user->ciudad);
            $user->direccion = strtolower($user->direccion);
            $user->genero = strtolower($user->genero);
            $user->cargo = strtolower($user->cargo);
            $user->estado = strtolower($user->estado);
        });
    }
}

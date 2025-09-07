<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
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
        'active_bodega_id'
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

//    public function bodega()
//    {
//        return $this->belongsToMany(\App\Models\Bodega::class, );
//    }

    public function activeBodega()
    {
        return $this->belongsTo(Bodega::class, 'active_bodega_id');
    }
}

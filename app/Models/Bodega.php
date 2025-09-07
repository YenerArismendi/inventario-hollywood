<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bodega extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'direccion', 'tipo', 'encargado_id'];

    /**
     * Obtiene el usuario encargado de la bodega.
     */
    public function encargado(): BelongsTo
    {
        return $this->belongsTo(User::class, 'encargado_id');
    }

    /**
     * Una Bodega puede tener muchos Usuarios (supervisores, empleados).
     */
//    public function users(): HasMany
//    {
//        return $this->hasMany(User::class);
//    }

    /**
     * Una Bodega tiene un inventario de muchos Productos.
     */
    public function productos(): BelongsToMany
    {
        return $this->belongsToMany(Producto::class, 'bodega_producto')->withPivot('cantidad')->withTimestamps();
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'bodega_user');
    }

    public function articles()
    {
        return $this->hasMany(Article::class);
    }
}

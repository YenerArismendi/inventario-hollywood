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

    public function users()
    {
        return $this->belongsToMany(User::class, 'bodega_user');
    }

    /**
     * Una Bodega tiene un inventario de muchos Artículos a través de una tabla pivote.
     */
    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'bodega_article')
            ->using(BodegaArticle::class)
            ->as('pivot')
            ->withPivot('stock', 'columna', 'fila')
            ->withTimestamps();
    }

    /**
     * Una Bodega puede tener muchas Cajas de venta.
     */
    public function cajas(): HasMany
    {
        return $this->hasMany(Caja::class);
    }

    /**
     * En una Bodega se pueden registrar muchas Ventas.
     */
    public function ventas(): HasMany
    {
        return $this->hasMany(Venta::class);
    }
}

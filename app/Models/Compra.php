<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Compra extends Model
{
    use HasFactory;

    protected $fillable = [
        'proveedor_id',
        'bodega_id',
        'fecha',
        'total',
    ];

    public function bodega()
    {
        return $this->belongsTo(Bodega::class);
    }

    public function proveedor()
    {
        return $this->belongsTo(Suppliers::class);
    }

    public function detalles()
    {
        return $this->hasMany(CompraDetalles::class, 'compra_id');
    }

    protected static function booted()
    {
        static::deleting(function ($compra) {
            $compra->detalles->each(function ($detalle) {
                $detalle->delete();
            });
        });
    }
}

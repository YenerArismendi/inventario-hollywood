<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Compra extends Model
{
    use HasFactory;

    protected $fillable = [
        'proveedor_id',
        'fecha',
        'total',
    ];

    public function proveedor()
    {
        return $this->belongsTo(Suppliers::class);
    }

    public function detalles()
    {
        return $this->hasMany(CompraDetalles::class);
    }
}

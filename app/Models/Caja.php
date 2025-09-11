<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Caja extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'bodega_id',
        'activa',
    ];

    public function bodega()
    {
        return $this->belongsTo(Bodega::class);
    }

    public function sesionesCaja()
    {
        return $this->hasMany(SesionCaja::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recetas extends Model
{
    Protected $table = 'recetas';
    Protected $fillable = ['nombre', 'precio', 'tipo'];

    protected $casts = [
        'detalles' => 'array',
    ];

    public function detalles()
    {
        return $this->hasMany(RecetaDetalles::class, 'receta_id');
    }



}

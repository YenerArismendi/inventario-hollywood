<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ubicacion extends Model
{
    protected $table = "ubicacions";

    protected $fillable = [
        'estante_id',
        'posicion',
        'articles_id',
    ];

    public function estante()
    {
        return $this->belongsTo(Estante::class);
    }

    public function producto()
    {
        return $this->belongsTo(Article::class, 'articles_id');
    }
}

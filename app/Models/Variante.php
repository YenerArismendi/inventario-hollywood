<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Variante extends Model
{

    Protected $table = 'variantes';
    Protected $primaryKey = 'id';
    Protected $fillable = ['nombre', 'descripcion', 'article_id'];

    public function article()
    {
        return $this->belongsTo(Article::class);
    }

}

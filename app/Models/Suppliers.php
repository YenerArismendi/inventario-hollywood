<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class Suppliers extends Model
{
    protected $table = 'suppliers';
    protected $fillable = ['id', 'name', 'responsible', 'email', 'phone', 'address'];


    // Relación inversa (opcional)
    public function articulos()
    {
        return $this->hasMany(Article::class, 'proveedor_id');
    }
}

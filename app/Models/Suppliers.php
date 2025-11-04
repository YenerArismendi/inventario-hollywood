<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Suppliers extends Model
{
    protected $table = 'suppliers';
    protected $fillable = ['name', 'responsible', 'email', 'phone', 'address'];

    protected function name(): Attribute
    {
        return Attribute::make(
            set: fn($value) => strtolower($value),
        );
    }

    protected function responsible(): Attribute
    {
        return Attribute::make(
            set: fn($value) => strtolower($value),
        );
    }

    protected
    function email(): Attribute
    {
        return Attribute::make(
            set: fn($value) => strtolower($value),
        );
    }

    protected
    function address(): Attribute
    {
        return Attribute::make(
            set: fn($value) => strtolower($value),
        );
    }

    // Relación inversa (opcional)
    public function articulos()
    {
        return $this->hasMany(Article::class, 'proveedor_id');
    }
}

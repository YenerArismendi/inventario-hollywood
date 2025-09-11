<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VentaDetalle extends Model
{
    use HasFactory;

    protected $fillable = [
        'venta_id',
        'article_id',
        'cantidad',
        'precio_unitario',
        'descuento_item',
        'subtotal_item',
    ];

    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }
}

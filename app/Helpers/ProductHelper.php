<?php
namespace App\Helpers;

use App\Models\Article;

class ProductHelper
{
    public static function generarCodigoProducto(string $nombre, string $tipoDetalle): string
    {
        // Abreviatura del tipo detalle (3 primeras letras en mayúscula)
        $detalle = strtoupper(substr($tipoDetalle, 0, 3));

        // Abreviatura del nombre (3 primeras letras en mayúscula)
        $abreviadoNombre = strtoupper(substr($nombre, 0, 3));

        // Buscar el consecutivo de ese detalle en la BD
        $count = Article::where('tipo_detalle', $tipoDetalle)->count() + 1;

        // Formatear el consecutivo en 3 dígitos (001, 002, ...)
        $consecutivo = str_pad($count, 3, '0', STR_PAD_LEFT);

        // Construir el código final
        return "{$detalle}-{$abreviadoNombre}-{$consecutivo}";
    }
}


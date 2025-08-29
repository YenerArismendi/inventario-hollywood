<?php

namespace App\Helpers;

use App\Models\Article;

class ProductHelper
{
    public static function generarCodigoProducto(string $nombre, ?int $id = null): string
    {
        // Tomamos las 3 primeras letras en mayúsculas
        $prefijo = strtoupper(substr($nombre, 0, 3));

        // Si estamos actualizando, verificamos si el código ya existe para este artículo
        if ($id) {
            $articulo = Article::find($id);
            if ($articulo && strpos($articulo->codigo, $prefijo) === 0) {
                return $articulo->codigo; // Si el prefijo coincide, no generamos uno nuevo
            }
        }

        // Contamos cuántos productos existen con ese prefijo
        $conteo = Article::where('codigo', 'like', $prefijo . '%')->count() + 1;

        // Retornamos el código final (ej: "PRO-001")
        return $prefijo . '-' . str_pad($conteo, 3, '0', STR_PAD_LEFT);
    }
}

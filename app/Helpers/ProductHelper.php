<?php

namespace App\Helpers;

use App\Models\Article;

class ProductHelper
{
    /**
     * Genera un código de producto único y descriptivo.
     *
     * @param string $nombre El nombre base del producto.
     * @param string $marca El nombre de la marca.
     * @param string|null $presentacion La presentación (ej: 120ml, 250g).
     * @return string El código único generado.
     */
    public static function generarCodigoUnico(string $nombre, string $marca, ?string $presentacion): string
    {
        // Limpiar y abreviar los componentes del código
        $nombreLimpio = preg_replace('/[^a-zA-Z0-9]/', '', $nombre);
        $marcaLimpia = preg_replace('/[^a-zA-Z0-9]/', '', $marca);
        $presentacionLimpia = preg_replace('/[^a-zA-Z0-9]/', '', $presentacion ?? '');

        $nombreCorto = strtoupper(substr($nombreLimpio, 0, 3));
        $marcaCorta = strtoupper(substr($marcaLimpia, 0, 3));
        $presentacionCorta = strtoupper(substr($presentacionLimpia, 0, 3));

        do {
            // Añadimos un componente aleatorio para asegurar la unicidad
            $aleatorio = strtoupper(substr(bin2hex(random_bytes(4)), 0, 4));
            $codigoBase = "{$marcaCorta}-{$nombreCorto}-{$presentacionCorta}-{$aleatorio}";
            $codigo = rtrim(str_replace('--', '-', $codigoBase), '-');

            // Comprobar si el código ya existe en la base de datos
            $existe = Article::where('codigo', $codigo)->exists();
        } while ($existe);

        return $codigo;
    }
}


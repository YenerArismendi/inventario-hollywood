<?php

namespace App\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ColombiaData
{
    /**
     * Obtiene todos los datos de departamentos y ciudades, usando caché.
     */
    public static function all(): Collection
    {
        return Cache::rememberForever('colombia_data', function () {
            // Construimos la ruta absoluta y directa al archivo.
            $path = storage_path('app/data/colombia.json');

            // Verificamos si el archivo es legible directamente por PHP.
            if (!is_readable($path)) {
                Log::error("Fallo al cargar los datos de Colombia: El archivo no se encuentra o no tiene permisos de lectura en la ruta: {$path}");
                return collect([]);
            }
            // Leemos el contenido del archivo directamente.
            $json = file_get_contents($path);
            $data = json_decode($json, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error("Error al decodificar el archivo JSON de Colombia: " . json_last_error_msg());
                return collect([]);
            }

            return collect($data);
        });
    }

    /**
     * Obtiene una lista de todos los departamentos.
     */
    public static function getDepartamentos(): array
    {
        return self::all()->pluck('departamento', 'departamento')->toArray();
    }

    /**
     * Obtiene las ciudades de un departamento específico.
     */
    public static function getCiudades(string $departamento): array
    {
        $ciudades = self::all()->firstWhere('departamento', $departamento)['ciudades'] ?? [];
        return array_combine($ciudades, $ciudades);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Ubicacion;

class Estante extends Model
{
    protected $fillable = ['bodega_id', 'nombre', 'nivel', 'filas', 'columnas'];

    protected static function booted()
    {
        // ✅ Cuando se crea un nuevo estante
        static::created(function ($estante) {
            $estante->generarUbicaciones();
        });

        // ✅ Cuando se actualiza un estante existente
        static::updated(function ($estante) {
            $estante->actualizarUbicaciones();
        });
    }

    // 🔹 Genera las ubicaciones según las filas y columnas
    public function generarUbicaciones()
    {
        for ($fila = 1; $fila <= $this->filas; $fila++) {
            for ($columna = 1; $columna <= $this->columnas; $columna++) {
                Ubicacion::firstOrCreate([
                    'estante_id' => $this->id,
                    'posicion' => chr(64 + $fila) . $columna, // A1, A2, B1, etc.
                ]);
            }
        }
    }

    // 🔹 Actualiza las ubicaciones si cambian filas o columnas
    public function actualizarUbicaciones()
    {
        $ubicacionesActuales = $this->ubicaciones()->pluck('posicion')->toArray();
        $nuevasUbicaciones = [];

        // Generar lista de posiciones esperadas (A1, A2, B1, etc.)
        for ($fila = 1; $fila <= $this->filas; $fila++) {
            for ($columna = 1; $columna <= $this->columnas; $columna++) {
                $nuevasUbicaciones[] = chr(64 + $fila) . $columna;
            }
        }

        // 🔸 Crear las nuevas ubicaciones que no existan
        foreach (array_diff($nuevasUbicaciones, $ubicacionesActuales) as $posicion) {
            Ubicacion::create([
                'estante_id' => $this->id,
                'posicion' => $posicion,
            ]);
        }

        // 🔸 Eliminar las ubicaciones sobrantes
        foreach (array_diff($ubicacionesActuales, $nuevasUbicaciones) as $posicion) {
            $this->ubicaciones()->where('posicion', $posicion)->delete();
        }
    }

    // 🔹 Relaciones
    public function bodega()
    {
        return $this->belongsTo(Bodega::class);
    }

    public function ubicaciones()
    {
        return $this->hasMany(Ubicacion::class);
    }
}

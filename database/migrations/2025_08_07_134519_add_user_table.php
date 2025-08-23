<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('telefono')->nullable();
            $table->string('documento_identidad')->unique()->nullable();
            $table->string('tipo_documento')->default('CC')->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->enum('genero', ['masculino', 'femenino', 'otro'])->nullable();
            $table->string('direccion')->nullable();
            $table->string('ciudad')->nullable();
            $table->string('cargo')->nullable();
            $table->boolean('estado')->default(true);
//            $table->foreignId('bodega_id')->nullable()->constrained('bodegas')->onDelete('set null'); pendiente en agregar ya que no ahi tabla de bodega
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Primero eliminamos la clave foránea y la columna relacionada
//            $table->dropForeign(['bodega_id']);
//            $table->dropColumn('bodega_id');

            // Luego eliminamos las demás columnas agregadas
            $table->dropColumn([
                'telefono',
                'documento_identidad',
                'tipo_documento',
                'fecha_nacimiento',
                'genero',
                'direccion',
                'ciudad',
                'cargo',
                'estado',
            ]);
        });
    }
};

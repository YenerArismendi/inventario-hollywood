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
        Schema::create('estantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bodega_id')->constrained()->cascadeOnDelete();
            $table->string('nombre');              // Nombre del estante
            $table->integer('nivel')->default(1);  // Nivel del estante
            $table->integer('filas');  // Número de filas (por ejemplo, A–E)
            $table->integer('columnas'); // Número de columnas (por ejemplo, 1–10)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estantes');
    }
};

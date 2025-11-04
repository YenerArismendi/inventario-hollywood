<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bodega_article', function (Blueprint $table) {
            $table->string('columna', 2)->nullable()->comment('Letra de la columna del estante (A, B, AA, etc.)');
            $table->unsignedTinyInteger('fila')->nullable()->comment('Número de la fila del estante');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bodega_article', function (Blueprint $table) {
            // Elimina las columnas si se revierte la migración
            $table->dropColumn(['columna', 'fila']);
        });
    }
};

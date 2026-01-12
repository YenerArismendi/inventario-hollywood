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
        Schema::create('transferencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bodega_origen_id')->constrained('bodegas');
            $table->foreignId('bodega_destino_id')->constrained('bodegas');
            $table->foreignId('user_despacho_id')->constrained('users');
            $table->foreignId('user_recepcion_id')->nullable()->constrained('users');
            $table->string('estado')->default('borrador'); // borrador, despachado, recibido_conforme, en_disputa, completado, cancelado
            $table->json('evidencia_despacho')->nullable();
            $table->json('evidencia_recepcion')->nullable();
            $table->text('observaciones_despacho')->nullable();
            $table->text('observaciones_recepcion')->nullable();
            $table->timestamp('fecha_despacho')->nullable();
            $table->timestamp('fecha_recepcion')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transferencias');
    }
};

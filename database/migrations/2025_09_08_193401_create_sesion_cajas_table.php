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
        Schema::create('sesion_cajas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caja_id')->constrained('cajas');
            $table->foreignId('user_id')->constrained('users')->comment('Usuario responsable de la sesión');
            $table->decimal('monto_inicial', 10, 2);
            $table->decimal('monto_final_calculado', 10, 2)->nullable();
            $table->decimal('monto_final_contado', 10, 2)->nullable();
            $table->decimal('diferencia', 10, 2)->nullable()->comment('Deuda o sobrante');
            $table->dateTime('fecha_apertura');
            $table->dateTime('fecha_cierre')->nullable();
            $table->enum('estado', ['abierta', 'cerrada', 'pendiente_aprobacion', 'aprobada'])->default('abierta');
            $table->foreignId('aprobado_por_id')->nullable()->constrained('users');
            $table->text('notas_cierre')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sesion_cajas');
    }
};

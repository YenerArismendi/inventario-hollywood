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
        Schema::create('movimiento_inventarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bodega_id')->constrained()->cascadeOnDelete();
            $table->foreignId('articles_id')->constrained()->cascadeOnDelete();
            $table->integer('cantidad'); // Puede ser positivo o negativo
            $table->string('tipo')->comment('ingreso_compra, salida_venta, ajuste, traslado');
            $table->string('estado')->default('pendiente_confirmacion'); // pendiente_confirmacion, confirmado, rechazado
            $table->text('observaciones')->nullable();
            $table->foreignId('creado_por_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('confirmado_por_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('confirmado_en')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimiento_inventarios');
    }
};

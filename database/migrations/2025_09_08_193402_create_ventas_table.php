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
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->comment('Usuario que realiza la venta');
            $table->foreignId('bodega_id')->constrained('bodegas')->comment('Bodega desde donde se vende');
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->comment('Cliente asociado (opcional)');
            $table->foreignId('sesion_caja_id')->constrained('sesion_cajas')->comment('Sesión de caja asociada');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('descuento', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->enum('metodo_pago', ['efectivo', 'transferencia', 'tarjeta', 'credito']);
            $table->enum('estado', ['completada', 'anulada'])->default('completada');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};

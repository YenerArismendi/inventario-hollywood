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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('nombre'); // Nombre del artículo
            $table->enum('tipo', ['producto', 'insumo']); // Para diferenciar
            $table->string('codigo')->nullable(); // Código interno o SKU
            $table->text('descripcion')->nullable(); // Descripción detallada
            $table->decimal('precio', 10, 2)->nullable(); // Precio base o de referencia
            $table->string('unidad_medida')->nullable(); // Unidad (kg, unidad, litro, caja, etc.)
                $table->string('imagen')->nullable(); // Ruta a imagen principal
            $table->boolean('estado')->default(true); // Estado (activo/inactivo)

            $table->foreignId('proveedor_id')
                ->nullable()
                ->constrained('suppliers')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};

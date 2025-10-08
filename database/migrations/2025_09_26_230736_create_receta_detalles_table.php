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
        Schema::create('receta_detalles', function (Blueprint $table) {
            $table->id();

            // Clave foránea hacia recetas
            $table->foreignId('receta_id')
                ->constrained('recetas')
                ->cascadeOnDelete();

            $table->foreignId('insumos_id')->constrained('insumos')->cascadeOnDelete();

            $table->integer('cantidad');
            $table->string('unidad');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receta_detalles');
    }
};

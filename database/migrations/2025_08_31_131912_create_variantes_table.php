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
        Schema::create('variantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained()->onDelete('cascade'); // Relación con artículos
            $table->string('medida')->nullable(); // Ej: cantidad ya sea en peso o en litros, solo para referencia
            $table->string('color')->nullable(); // Ej: Color del material
            $table->string('material')->nullable(); // Ej: Tipo del material
            $table->string('calidad')->nullable(); // Ej: Tipo del material
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variantes');
    }
};

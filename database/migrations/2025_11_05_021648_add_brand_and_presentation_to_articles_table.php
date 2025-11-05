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
        Schema::table('articles', function (Blueprint $table) {
            // Añadir la relación con la marca
            $table->foreignId('brand_id')->nullable()->after('category_id')->constrained('brands')->onDelete('set null');
            // Añadir el campo para la presentación (tamaño, color, etc.)
            $table->string('presentation')->nullable()->after('nombre');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropForeign(['brand_id']);
            $table->dropColumn(['brand_id', 'presentation']);
        });
    }
};

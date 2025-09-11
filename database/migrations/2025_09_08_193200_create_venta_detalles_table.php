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
        Schema::create('venta_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')->constrained('ventas')->cascadeOnDelete();
            $table->foreignId('article_id')->constrained('articles'); // Usamos tu modelo 'Article' existente
            $table->integer('cantidad');
            $table->decimal('precio_unitario', 10, 2)->comment('Precio al momento de la venta');
            $table->decimal('descuento_item', 10, 2)->default(0);
            $table->decimal('subtotal_item', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venta_detalles');
    }
};

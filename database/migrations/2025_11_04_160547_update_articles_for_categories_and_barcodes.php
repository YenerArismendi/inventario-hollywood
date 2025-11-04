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
        Schema::table('articles', function (Blueprint $table) {
            // 1. Añadir nuevos campos
            $table->string('codigo_barras')->unique()->nullable()->after('codigo');
            $table->decimal('costo', 10, 2)->nullable()->after('descripcion');
            $table->decimal('precio_venta', 10, 2)->default(0)->after('costo');
            $table->string('codigo_qr')->nullable()->after('imagen');
            $table->foreignId('category_id')->nullable()->after('temporada')->constrained('categories')->onDelete('set null');

            // 2. Modificar columnas existentes
            $table->string('codigo')->unique()->comment('Código interno autogenerado para QR y gestión')->change();
            $table->dropForeign(['proveedor_id']); // Eliminar la vieja restricción
            $table->foreign('proveedor_id')->references('id')->on('suppliers')->onDelete('set null'); // Añadir la nueva

            // 3. Eliminar columnas viejas
            $table->dropColumn('tipo');
            $table->dropColumn('precio');

            // 4. Añadir índices para búsquedas rápidas
            $table->index('codigo_barras');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            // Revertir los cambios en el orden inverso
            $table->dropIndex(['codigo_barras']);
            $table->enum('tipo', ['producto', 'insumo'])->after('nombre');
            $table->decimal('precio', 10, 2)->nullable()->after('descripcion');

            $table->dropForeign(['category_id']);

            $table->dropColumn(['codigo_barras', 'costo', 'precio_venta', 'codigo_qr', 'category_id']);
        });
    }
};

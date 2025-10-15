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
        Schema::table('sesion_cajas', function (Blueprint $table) {
            // Renombramos la columna para mayor claridad
            $table->renameColumn('monto_final_calculado', 'monto_final_efectivo_calculado');

            // Añadimos las nuevas columnas después de 'monto_inicial'
            $table->decimal('total_ventas_efectivo', 10, 2)->default(0)->after('monto_inicial');
            $table->decimal('total_ventas_transferencia', 10, 2)->default(0)->after('total_ventas_efectivo');
            $table->decimal('total_ventas_credito', 10, 2)->default(0)->after('total_ventas_tarjeta');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sesion_cajas', function (Blueprint $table) {
            $table->renameColumn('monto_final_efectivo_calculado', 'monto_final_calculado');
            $table->dropColumn([
                'total_ventas_efectivo',
                'total_ventas_transferencia',
                'total_ventas_credito',
            ]);
        });
    }
};

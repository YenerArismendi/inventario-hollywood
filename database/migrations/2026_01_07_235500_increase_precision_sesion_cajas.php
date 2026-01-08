<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Using raw SQL to ensure precision change works without doctrine/dbal
        // and to be explicit about the new definition.

        $table = 'sesion_cajas';

        // Helper to generate the alter statement
        // Modify columns to DECIMAL(20, 2)

        // We need to list current columns.
        // from migration 1: monto_inicial, monto_final_calculado (renamed), monto_final_contado, diferencia
        // from migration 2: total_ventas_efectivo, total_ventas_transferencia, total_ventas_credito
        // renamed: monto_final_calculado -> monto_final_efectivo_calculado

        $columns = [
            'monto_inicial' => 'DECIMAL(20, 2) NOT NULL',
            'monto_final_efectivo_calculado' => 'DECIMAL(20, 2) NULL',
            'monto_final_contado' => 'DECIMAL(20, 2) NULL',
            'diferencia' => 'DECIMAL(20, 2) NULL COMMENT \'Deuda o sobrante\'',
            'total_ventas_efectivo' => 'DECIMAL(20, 2) NOT NULL DEFAULT 0',
            'total_ventas_transferencia' => 'DECIMAL(20, 2) NOT NULL DEFAULT 0',
            'total_ventas_credito' => 'DECIMAL(20, 2) NOT NULL DEFAULT 0',
        ];

        foreach ($columns as $column => $definition) {
            // Check if column exists before modifying to be safe, though we know they exist.
            // However, raw SQL in Laravel migrations for MySQL is straightforward.
            DB::statement("ALTER TABLE `{$table}` MODIFY COLUMN `{$column}` {$definition}");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $table = 'sesion_cajas';

        // Revert to DECIMAL(10, 2)
        $columns = [
            'monto_inicial' => 'DECIMAL(10, 2) NOT NULL',
            'monto_final_efectivo_calculado' => 'DECIMAL(10, 2) NULL',
            'monto_final_contado' => 'DECIMAL(10, 2) NULL',
            'diferencia' => 'DECIMAL(10, 2) NULL COMMENT \'Deuda o sobrante\'',
            'total_ventas_efectivo' => 'DECIMAL(10, 2) NOT NULL DEFAULT 0',
            'total_ventas_transferencia' => 'DECIMAL(10, 2) NOT NULL DEFAULT 0',
            'total_ventas_credito' => 'DECIMAL(10, 2) NOT NULL DEFAULT 0',
        ];

        foreach ($columns as $column => $definition) {
            DB::statement("ALTER TABLE `{$table}` MODIFY COLUMN `{$column}` {$definition}");
        }
    }
};

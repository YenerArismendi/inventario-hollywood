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
        Schema::table('transferencia_detalles', function (Blueprint $table) {
            $table->foreignId('article_id')->nullable()->change();
            $table->foreignId('insumo_id')->nullable()->after('article_id')->constrained('insumos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transferencia_detalles', function (Blueprint $table) {
            $table->dropForeign(['insumo_id']);
            $table->dropColumn('insumo_id');
            $table->foreignId('article_id')->nullable(false)->change();
        });
    }
};

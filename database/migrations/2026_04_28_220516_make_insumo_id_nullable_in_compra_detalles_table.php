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
        Schema::table('compra_detalles', function (Blueprint $table) {
            $table->unsignedBigInteger('insumo_id')->nullable()->change();
            if (!Schema::hasColumn('compra_detalles', 'article_id')) {
                $table->unsignedBigInteger('article_id')->nullable()->after('insumo_id');
            } else {
                $table->unsignedBigInteger('article_id')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('compra_detalles', function (Blueprint $table) {
            $table->unsignedBigInteger('insumo_id')->nullable(false)->change();
            if (Schema::hasColumn('compra_detalles', 'article_id')) {
                $table->dropColumn('article_id');
            }
        });
    }
};

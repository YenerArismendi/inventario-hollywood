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
        Schema::table('users', function (Blueprint $table) {
            // La bodega es opcional porque el admin principal puede no estar asignado a ninguna.
            // nullOnDelete() significa que si se borra la bodega, el campo en el usuario se pone a null.
            $table->foreignId('bodega_id')->nullable()->after('id')->constrained()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Para revertir, primero eliminamos la llave foránea y luego la columna.
            $table->dropForeign(['bodega_id']);
            $table->dropColumn('bodega_id');
        });
    }
};

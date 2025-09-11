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
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('documento_identidad')->nullable()->unique();
            $table->string('telefono')->nullable();
            $table->string('email')->nullable()->unique();
            $table->boolean('tiene_credito')->default(false);
            $table->decimal('limite_credito', 10, 2)->default(0);
            $table->foreignId('user_id')->nullable()->unique()->constrained('users')->nullOnDelete(); // Vínculo para futuro e-commerce
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};

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
       $table->foreignId('user_id')->nullable()->unique()->constrained('users')->nullOnDelete();
       $table->string('nombre', 100);
       $table->string('apellidos', 100);
       $table->string('telefono', 15);
       $table->string('direccion', 200);
       $table->string('ciudad', 80);
       $table->string('estado', 80);
       $table->char('codigo_postal', 5);
       $table->boolean('activo')->default(true);
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

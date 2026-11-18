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
        Schema::create('productos', function (Blueprint $table) {
       $table->id();
       $table->foreignId('categoria_id')->constrained('categorias')->restrictOnDelete();
       $table->foreignId('proveedor_id')->constrained('proveedores')->restrictOnDelete();
       $table->string('nombre', 120);
       $table->string('sku', 30)->unique();
       $table->text('descripcion')->nullable();
       $table->string('guardian', 30)->nullable();
       $table->decimal('precio', 10, 2);
       $table->unsignedInteger('stock')->default(0);
       $table->string('imagen', 255)->nullable();
       $table->boolean('activo')->default(true);
       $table->timestamps();
   });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};

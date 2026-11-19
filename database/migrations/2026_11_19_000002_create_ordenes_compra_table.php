<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ordenes_compra', function (Blueprint $table) {
            $table->id();
            $table->string('folio', 20)->unique();
            $table->foreignId('proveedor_id')->constrained('proveedores')->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->string('estado', 20)->default('borrador');
            $table->decimal('total', 10, 2)->default(0);
            $table->string('notas', 200)->nullable();
            $table->timestamp('enviada_at')->nullable();
            $table->timestamp('recibida_at')->nullable();
            $table->timestamp('cancelada_at')->nullable();
            $table->timestamps();

            $table->index(['estado', 'created_at']);
        });

        Schema::create('orden_compra_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orden_compra_id')->constrained('ordenes_compra')->cascadeOnDelete();
            $table->foreignId('producto_id')->constrained('productos')->restrictOnDelete();
            $table->string('nombre', 120);
            $table->unsignedInteger('cantidad');
            $table->decimal('costo_unitario', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orden_compra_detalles');
        Schema::dropIfExists('ordenes_compra');
    }
};

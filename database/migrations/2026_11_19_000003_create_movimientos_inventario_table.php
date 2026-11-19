<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movimientos_inventario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos')->cascadeOnDelete();
            $table->string('tipo', 10);              // entrada | salida
            $table->integer('cantidad');             // siempre positiva; "tipo" dice si suma o resta
            $table->integer('stock_resultante');     // cuánto quedó después del movimiento
            $table->string('motivo', 30);            // compra, venta, cancelacion, ajuste, inventario_inicial
            $table->string('referencia', 30)->nullable(); // folio de la venta u orden de compra
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['producto_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos_inventario');
    }
};

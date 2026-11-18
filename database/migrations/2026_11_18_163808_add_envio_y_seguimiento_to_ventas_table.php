<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('costo_envio', 10, 2)->default(0);

            // Dirección estructurada
            $table->string('destinatario', 100)->nullable();
            $table->string('telefono', 10)->nullable();
            $table->string('calle_numero', 120)->nullable();
            $table->string('colonia', 80)->nullable();
            $table->string('codigo_postal', 5)->nullable();
            $table->string('ciudad', 80)->nullable();
            $table->string('estado_republica', 40)->nullable();
            $table->string('referencias', 200)->nullable();

            // Envío
            $table->string('paqueteria', 40)->nullable();
            $table->string('numero_guia', 40)->nullable();

            // Términos aceptados y línea de tiempo
            $table->timestamp('acepto_terminos_at')->nullable();
            $table->timestamp('pagada_at')->nullable();
            $table->timestamp('enviada_at')->nullable();
            $table->timestamp('entregada_at')->nullable();
            $table->timestamp('cancelada_at')->nullable();
        });

        // Las ventas anteriores no tenían envío: su subtotal es su total
        DB::table('ventas')->update(['subtotal' => DB::raw('total')]);
    }

    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropColumn([
                'subtotal', 'costo_envio', 'destinatario', 'telefono', 'calle_numero', 'colonia',
                'codigo_postal', 'ciudad', 'estado_republica', 'referencias', 'paqueteria',
                'numero_guia', 'acepto_terminos_at', 'pagada_at', 'enviada_at', 'entregada_at', 'cancelada_at',
            ]);
        });
    }
};
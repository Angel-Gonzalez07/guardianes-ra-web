<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('proveedores', function (Blueprint $table) {
            $table->string('rfc', 13)->nullable()->unique();
            $table->string('ciudad', 80)->nullable();
            $table->string('estado_republica', 40)->nullable();
            $table->unsignedSmallInteger('dias_entrega')->nullable();
            $table->string('condiciones_pago', 40)->nullable();
            $table->text('notas')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('proveedores', function (Blueprint $table) {
            $table->dropColumn(['rfc', 'ciudad', 'estado_republica', 'dias_entrega', 'condiciones_pago', 'notas']);
        });
    }
};

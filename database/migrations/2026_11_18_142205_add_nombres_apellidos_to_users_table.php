<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nombres', 60)->nullable()->after('name');
            $table->string('apellidos', 60)->nullable()->after('nombres');
        });

        // Separa los nombres que ya existen: primera palabra = nombres, el resto = apellidos
        DB::table('users')->get()->each(function ($u) {
            $partes = preg_split('/\s+/u', trim($u->name), 2);
            DB::table('users')->where('id', $u->id)->update([
                'nombres'   => mb_substr($partes[0] ?? '', 0, 60),
                'apellidos' => mb_substr($partes[1] ?? '', 0, 60),
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nombres', 'apellidos']);
        });
    }
};
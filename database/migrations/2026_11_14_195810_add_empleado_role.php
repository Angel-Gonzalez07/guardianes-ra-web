<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (! DB::table('roles')->where('nombre', 'empleado')->exists()) {
            DB::table('roles')->insert([
                'nombre'     => 'empleado',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('roles')->where('nombre', 'empleado')->delete();
    }
};

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RolesYAdminSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->insert([
            ['nombre' => 'admin',   'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'cliente', 'created_at' => now(), 'updated_at' => now()],
        ]);

        $adminId = DB::table('roles')->where('nombre', 'admin')->value('id');

        DB::table('users')->insert([
            'name'       => 'Administrador',
            'email'      => 'admin@utcv.edu.mx',
            'password'   => Hash::make('Admin12345'),
            'role_id'    => $adminId,
            'activo'     => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
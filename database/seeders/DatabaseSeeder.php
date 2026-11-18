<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RolesYAdminSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['admin', 'empleado', 'cliente'] as $rol) {
            if (! DB::table('roles')->where('nombre', $rol)->exists()) {
                DB::table('roles')->insert(['nombre' => $rol, 'created_at' => now(), 'updated_at' => now()]);
            }
        }

        $cuentas = [
            ['Administrador', 'admin@utcv.edu.mx',    'Admin12345',    'admin'],
            ['Empleado Demo', 'empleado@utcv.edu.mx', 'Empleado12345', 'empleado'],
            ['Cliente Demo',  'cliente@utcv.edu.mx',  'Cliente12345',  'cliente'],
        ];

        foreach ($cuentas as [$nombre, $correo, $clave, $rol]) {
            if (DB::table('users')->where('email', $correo)->exists()) {
                continue; // no pisa cuentas existentes
            }

            DB::table('users')->insert([
                'name'       => $nombre,
                'email'      => $correo,
                'password'   => Hash::make($clave),
                'role_id'    => DB::table('roles')->where('nombre', $rol)->value('id'),
                'activo'     => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Producto;
use App\Models\Proveedor;
use Illuminate\Database\Seeder;

class TiendaDemoSeeder extends Seeder
{
    public function run(): void
    {
        $figuras = Categoria::firstOrCreate(['nombre' => 'Figuras'], ['descripcion' => 'Figuras coleccionables de los guardianes', 'activo' => true]);
        Categoria::firstOrCreate(['nombre' => 'Peluches'], ['descripcion' => 'Peluches de los guardianes (próximamente)', 'activo' => true]);
        Categoria::firstOrCreate(['nombre' => 'Camisas'], ['descripcion' => 'Camisas de los guardianes (próximamente)', 'activo' => true]);

        $proveedor = Proveedor::firstOrCreate(
            ['nombre' => 'Proveedor Demo'],
            ['contacto' => 'Contacto Demo', 'email' => 'ventas@proveedordemo.com', 'telefono' => '2711234567', 'direccion' => 'Córdoba, Veracruz', 'activo' => true]
        );

        // Precios de ejemplo: cámbialos desde el catálogo de Productos.
        $productos = [
            ['FIG-MUS-001', 'Figura Musguín', 'Musguín', 349.00],
            ['FIG-GOT-001', 'Figura Goti',    'Goti',    349.00],
            ['FIG-LUM-001', 'Figura Lumi',    'Lumi',    349.00],
            ['FIG-TRO-001', 'Figura Tronqui', 'Tronqui', 399.00],
        ];

        foreach ($productos as [$sku, $nombre, $guardian, $precio]) {
            Producto::firstOrCreate(['sku' => $sku], [
                'categoria_id' => $figuras->id,
                'proveedor_id' => $proveedor->id,
                'nombre'       => $nombre,
                'descripcion'  => 'Figura coleccionable de ' . $guardian . ', guardián del ecosistema.',
                'guardian'     => $guardian,
                'precio'       => $precio,
                'stock'        => 15,
                'activo'       => true,
            ]);
        }
    }
}
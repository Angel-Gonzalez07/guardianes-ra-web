<?php

return [
    // Envío gratis a partir de este subtotal; si no, se cobra el costo fijo
    'envio_gratis_desde' => 999.00,
    'costo_envio'        => 99.00,

    // Datos para pagar por transferencia (cámbialos por los reales)
    'banco' => [
        'nombre'  => 'BBVA',
        'titular' => 'Raíces Creativas',
        'cuenta'  => '0123456789',
        'clabe'   => '012345678901234567',
    ],

    'paqueterias' => ['Estafeta', 'DHL', 'FedEx', 'Paquetexpress', 'Correos de México', 'Redpack'],

    'estados' => [
        'Aguascalientes', 'Baja California', 'Baja California Sur', 'Campeche', 'Chiapas',
        'Chihuahua', 'Ciudad de México', 'Coahuila', 'Colima', 'Durango', 'Guanajuato',
        'Guerrero', 'Hidalgo', 'Jalisco', 'México', 'Michoacán', 'Morelos', 'Nayarit',
        'Nuevo León', 'Oaxaca', 'Puebla', 'Querétaro', 'Quintana Roo', 'San Luis Potosí',
        'Sinaloa', 'Sonora', 'Tabasco', 'Tamaulipas', 'Tlaxcala', 'Veracruz', 'Yucatán', 'Zacatecas',
    ],
];
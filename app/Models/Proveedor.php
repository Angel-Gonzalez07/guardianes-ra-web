<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Proveedor extends Model
{
    protected $table = 'proveedores';

    protected $fillable = [
        'nombre', 'contacto', 'email', 'telefono', 'direccion',
        'rfc', 'ciudad', 'estado_republica', 'dias_entrega', 'condiciones_pago', 'notas',
        'activo',
    ];

    protected $casts = [
        'activo'       => 'boolean',
        'dias_entrega' => 'integer',
    ];

    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class);
    }

    public function ordenes(): HasMany
    {
        return $this->hasMany(OrdenCompra::class, 'proveedor_id');
    }
}

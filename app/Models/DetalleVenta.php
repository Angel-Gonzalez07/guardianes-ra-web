<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetalleVenta extends Model
{
    // Laravel adivinaría "detalle_ventas"; nuestra tabla se llama distinto
    protected $table = 'venta_detalles';

    protected $fillable = ['venta_id', 'producto_id', 'nombre', 'precio_unitario', 'cantidad', 'subtotal'];

    protected $casts = [
        'precio_unitario' => 'decimal:2',
        'subtotal'        => 'decimal:2',
        'cantidad'        => 'integer',
    ];

    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class);
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrdenCompraDetalle extends Model
{
    protected $table = 'orden_compra_detalles';

    protected $fillable = ['orden_compra_id', 'producto_id', 'nombre', 'cantidad', 'costo_unitario', 'subtotal'];

    protected $casts = [
        'cantidad'       => 'integer',
        'costo_unitario' => 'decimal:2',
        'subtotal'       => 'decimal:2',
    ];

    public function orden(): BelongsTo
    {
        return $this->belongsTo(OrdenCompra::class, 'orden_compra_id');
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MovimientoInventario extends Model
{
    protected $table = 'movimientos_inventario';

    public const MOTIVOS = [
        'compra'             => 'Compra a proveedor',
        'venta'              => 'Venta',
        'cancelacion'        => 'Cancelación de pedido',
        'ajuste'             => 'Ajuste manual',
        'inventario_inicial' => 'Inventario inicial',
    ];

    protected $fillable = ['producto_id', 'tipo', 'cantidad', 'stock_resultante', 'motivo', 'referencia', 'user_id'];

    protected $casts = [
        'cantidad'         => 'integer',
        'stock_resultante' => 'integer',
    ];

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

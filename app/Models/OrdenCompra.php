<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrdenCompra extends Model
{
    protected $table = 'ordenes_compra';

    public const ESTADOS = ['borrador', 'enviada', 'recibida', 'cancelada'];

    // Cambios de estado permitidos
    public const TRANSICIONES = [
        'borrador'  => ['enviada', 'cancelada'],
        'enviada'   => ['recibida', 'cancelada'],
        'recibida'  => [],
        'cancelada' => [],
    ];

    protected $fillable = [
        'folio', 'proveedor_id', 'user_id', 'estado', 'total', 'notas',
        'enviada_at', 'recibida_at', 'cancelada_at',
    ];

    protected $casts = [
        'total'        => 'decimal:2',
        'enviada_at'   => 'datetime',
        'recibida_at'  => 'datetime',
        'cancelada_at' => 'datetime',
    ];

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(OrdenCompraDetalle::class, 'orden_compra_id');
    }
}

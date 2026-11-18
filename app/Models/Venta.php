<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Venta extends Model
{
    public const ESTADOS = ['pendiente', 'pagada', 'enviada', 'entregada', 'cancelada'];

    // Estados que cuentan como dinero cobrado
    public const CONFIRMADAS = ['pagada', 'enviada', 'entregada'];

    public const METODOS = ['Tarjeta', 'Transferencia', 'Pago contra entrega'];

    // Cambios de estado permitidos
    public const TRANSICIONES = [
        'pendiente' => ['pagada', 'cancelada'],
        'pagada'    => ['enviada', 'cancelada'],
        'enviada'   => ['entregada'],
        'entregada' => [],
        'cancelada' => [],
    ];

    protected $fillable = ['folio', 'user_id', 'total', 'estado', 'metodo_pago', 'direccion_envio'];

    protected $casts = ['total' => 'decimal:2'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(DetalleVenta::class);
    }

    // Uso: Venta::confirmadas()->sum('total')
    public function scopeConfirmadas(Builder $query): Builder
    {
        return $query->whereIn('estado', self::CONFIRMADAS);
    }
}
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

    public const METODOS = ['Transferencia', 'Pago contra entrega'];

    // Cambios de estado permitidos
    public const TRANSICIONES = [
        'pendiente' => ['pagada', 'cancelada'],
        'pagada'    => ['enviada', 'cancelada'],
        'enviada'   => ['entregada'],
        'entregada' => [],
        'cancelada' => [],
    ];

    protected $fillable = [
        'folio', 'user_id', 'subtotal', 'costo_envio', 'total', 'estado', 'metodo_pago', 'direccion_envio',
        'destinatario', 'telefono', 'calle_numero', 'colonia', 'codigo_postal', 'ciudad', 'estado_republica', 'referencias',
        'paqueteria', 'numero_guia',
        'acepto_terminos_at', 'pagada_at', 'enviada_at', 'entregada_at', 'cancelada_at',
    ];

    protected $casts = [
        'subtotal'           => 'decimal:2',
        'costo_envio'        => 'decimal:2',
        'total'              => 'decimal:2',
        'acepto_terminos_at' => 'datetime',
        'pagada_at'          => 'datetime',
        'enviada_at'         => 'datetime',
        'entregada_at'       => 'datetime',
        'cancelada_at'       => 'datetime',
    ];

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

    /** Costo de envío según el subtotal: gratis arriba del monto configurado. */
    public static function costoEnvioPara(float $subtotal): float
    {
        return $subtotal >= (float) config('tienda.envio_gratis_desde')
            ? 0.0
            : (float) config('tienda.costo_envio');
    }

    /** Dirección en una sola línea (para el panel y los comprobantes). */
    public function direccionCompleta(): string
    {
        if (! $this->calle_numero) {
            return (string) $this->direccion_envio; // pedidos anteriores
        }

        return trim(
            $this->calle_numero . ', Col. ' . $this->colonia . ', C.P. ' . $this->codigo_postal . ', '
            . $this->ciudad . ', ' . $this->estado_republica
        );
    }

    /** Pasos de la línea de tiempo con su fecha (null = todavía no ocurre). */
    public function lineaDeTiempo(): array
    {
        return [
            ['clave' => 'pendiente', 'titulo' => 'Pedido realizado', 'fecha' => $this->created_at],
            ['clave' => 'pagada',    'titulo' => 'Pago confirmado',  'fecha' => $this->pagada_at],
            ['clave' => 'enviada',   'titulo' => 'Enviado',          'fecha' => $this->enviada_at],
            ['clave' => 'entregada', 'titulo' => 'Entregado',        'fecha' => $this->entregada_at],
        ];
    }
}
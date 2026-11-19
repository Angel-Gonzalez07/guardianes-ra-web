<?php

namespace App\Support;

use App\Models\MovimientoInventario;
use App\Models\Producto;
use Illuminate\Support\Facades\Auth;

class Inventario
{
    /**
     * Cambia el stock (positivo = entra, negativo = sale) y deja el movimiento registrado.
     * Llamar siempre dentro de una transacción (DB::transaction).
     */
    public static function mover(int $productoId, int $cantidad, string $motivo, ?string $referencia = null): void
    {
        if ($cantidad === 0) {
            return;
        }

        $producto = Producto::whereKey($productoId)->lockForUpdate()->firstOrFail();
        $nuevo = $producto->stock + $cantidad;

        if ($nuevo < 0) {
            throw new \DomainException('No hay stock suficiente de «' . $producto->nombre . '».');
        }

        $producto->update(['stock' => $nuevo]);
        self::registrar($producto, $cantidad, $motivo, $referencia);
    }

    /** Solo deja el registro: sirve cuando el stock ya cambió (por ejemplo, al editar el producto). */
    public static function registrar(Producto $producto, int $cantidad, string $motivo, ?string $referencia = null): void
    {
        if ($cantidad === 0) {
            return;
        }

        MovimientoInventario::create([
            'producto_id'      => $producto->id,
            'tipo'             => $cantidad > 0 ? 'entrada' : 'salida',
            'cantidad'         => abs($cantidad),
            'stock_resultante' => (int) $producto->stock,
            'motivo'           => $motivo,
            'referencia'       => $referencia,
            'user_id'          => Auth::id(),
        ]);
    }
}

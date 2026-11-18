@extends('layouts.tienda')

@section('titulo', 'Mi carrito')

@section('contenido')
    <h1 class="tit">Mi carrito</h1>

    @if ($items->isEmpty())
        <div class="tarjeta" style="text-align:center">
            <p>Tu carrito está vacío.</p>
            <p style="margin-top:1rem"><a class="btn" href="{{ route('tienda') }}">Ir a la tienda</a></p>
        </div>
    @else
        <div class="tarjeta tabla-wrap">
            <table>
                <thead><tr><th>Producto</th><th>Precio</th><th>Cantidad</th><th>Subtotal</th><th></th></tr></thead>
                <tbody>
                    @foreach ($items as $i)
                        <tr>
                            <td><strong>{{ $i['producto']->nombre }}</strong></td>
                            <td>${{ number_format($i['producto']->precio, 2) }}</td>
                            <td>
                                <form method="POST" action="{{ route('tienda.actualizar', $i['producto']) }}" style="display:flex;gap:.4rem">
                                    @csrf
                                    @method('PATCH')
                                    <input type="number" name="cantidad" value="{{ $i['cantidad'] }}" min="1" max="{{ min(10, $i['producto']->stock) }}" style="width:70px">
                                    <button class="btn btn-verde btn-sm" type="submit">Actualizar</button>
                                </form>
                            </td>
                            <td>${{ number_format($i['subtotal'], 2) }}</td>
                            <td>
                                <form method="POST" action="{{ route('tienda.quitar', $i['producto']) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-rojo btn-sm" type="submit">Quitar</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    <tr><td colspan="3" style="text-align:right"><strong>Total</strong></td><td colspan="2"><strong style="font-size:1.2rem">${{ number_format($total, 2) }}</strong></td></tr>
                </tbody>
            </table>
        </div>

        <div class="tarjeta" style="margin-top:1.2rem;max-width:560px">
            <h2 style="color:var(--verde)">Confirmar pedido</h2>
            <form method="POST" action="{{ route('tienda.confirmar') }}">
                @csrf
                <label for="metodo_pago">Método de pago</label>
                <select id="metodo_pago" name="metodo_pago" required>
                    <option value="">— Elige uno —</option>
                    @foreach ($metodos as $m)
                        <option value="{{ $m }}" @selected(old('metodo_pago') === $m)>{{ $m }}</option>
                    @endforeach
                </select>

                <label for="direccion_envio">Dirección de envío</label>
                <textarea id="direccion_envio" name="direccion_envio" rows="3" required minlength="10" maxlength="200">{{ old('direccion_envio', $direccion) }}</textarea>

                <p style="margin-top:.8rem;font-size:.85rem">El pago se confirma con el personal de la tienda; tu pedido queda como «pendiente».</p>
                <button class="btn" type="submit" style="margin-top:1rem">Realizar pedido por ${{ number_format($total, 2) }}</button>
            </form>
        </div>
    @endif
@endsection
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
        @include('tienda._pasos', ['actual' => 1])

        <div class="compra">
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
                                        <input type="number" name="cantidad" value="{{ $i['cantidad'] }}" min="1" max="{{ min(10, $i['producto']->stock) }}" style="width:70px" aria-label="Cantidad">
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
                    </tbody>
                </table>
            </div>

            <aside class="tarjeta resumen-c">
                <h2>Resumen</h2>
                <div class="linea"><span>Subtotal</span><span>${{ number_format($subtotal, 2) }}</span></div>
                <div class="linea"><span>Envío</span><span>{{ $envio > 0 ? '$' . number_format($envio, 2) : 'Gratis' }}</span></div>
                <div class="linea total"><span>Total</span><span>${{ number_format($total, 2) }}</span></div>
                @if ($envio > 0)
                    <p class="nota">Envío gratis en compras desde ${{ number_format(config('tienda.envio_gratis_desde'), 2) }}.</p>
                @endif
                <div class="acciones-c">
                    <a class="btn" href="{{ route('tienda.envio') }}">Continuar con el envío</a>
                    <a class="btn btn-verde" href="{{ route('tienda') }}">Seguir comprando</a>
                </div>
            </aside>
        </div>
    @endif
@endsection
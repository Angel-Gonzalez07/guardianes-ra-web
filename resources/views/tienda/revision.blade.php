@extends('layouts.tienda')

@section('titulo', 'Revisar pedido')

@section('contenido')
    <h1 class="tit">Revisa tu pedido</h1>
    <p class="sub">Confirma que todo esté correcto antes de realizar el pedido.</p>

    @include('tienda._pasos', ['actual' => 4])

    <div class="compra">
        <div>
            <div class="tarjeta" style="margin-bottom:1.2rem">
                <h2 style="color:var(--verde);font-size:1.25rem">Productos</h2>
                <div class="tabla-wrap">
                    <table>
                        <thead><tr><th>Producto</th><th>Precio</th><th>Cantidad</th><th>Subtotal</th></tr></thead>
                        <tbody>
                            @foreach ($items as $i)
                                <tr>
                                    <td><strong>{{ $i['producto']->nombre }}</strong></td>
                                    <td>${{ number_format($i['producto']->precio, 2) }}</td>
                                    <td>{{ $i['cantidad'] }}</td>
                                    <td>${{ number_format($i['subtotal'], 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <p style="margin-top:.8rem"><a href="{{ route('tienda.carrito') }}" style="color:var(--verde);font-weight:800">Editar carrito</a></p>
            </div>

            <div class="tarjeta" style="margin-bottom:1.2rem">
                <h2 style="color:var(--verde);font-size:1.25rem">Envío</h2>
                <p><strong>{{ $envioD['destinatario'] }}</strong> · Tel. {{ $envioD['telefono'] }}<br>
                   {{ $envioD['calle_numero'] }}, {{ $envioD['colonia'] }}<br>
                   C.P. {{ $envioD['codigo_postal'] }}, {{ $envioD['ciudad'] }}, {{ $envioD['estado_republica'] }}
                   @if (! empty($envioD['referencias']))<br>Referencias: {{ $envioD['referencias'] }}@endif</p>
                <p style="margin-top:.8rem"><a href="{{ route('tienda.envio') }}" style="color:var(--verde);font-weight:800">Cambiar dirección</a></p>
            </div>

            <div class="tarjeta">
                <h2 style="color:var(--verde);font-size:1.25rem">Pago</h2>
                <p>{{ $metodo }}</p>
                <p style="margin-top:.8rem"><a href="{{ route('tienda.pago') }}" style="color:var(--verde);font-weight:800">Cambiar método de pago</a></p>
            </div>
        </div>

        <form class="tarjeta resumen-c" method="POST" action="{{ route('tienda.confirmar') }}">
            @csrf
            <h2>Total a pagar</h2>
            <div class="linea"><span>Subtotal</span><span>${{ number_format($subtotal, 2) }}</span></div>
            <div class="linea"><span>Envío</span><span>{{ $envio > 0 ? '$' . number_format($envio, 2) : 'Gratis' }}</span></div>
            <div class="linea total"><span>Total</span><span>${{ number_format($total, 2) }}</span></div>

            <details style="margin-top:.8rem;font-size:.85rem">
                <summary style="cursor:pointer;font-weight:800">Términos y aviso de privacidad</summary>
                <p style="margin-top:.4rem">Los precios están en pesos mexicanos. Tu pedido se aparta al realizarlo y se confirma cuando el personal verifica el pago. Mientras esté pendiente puedes cancelarlo desde «Mis pedidos»; una vez pagado, comunícate con la tienda. Usamos tus datos únicamente para procesar, enviar y dar seguimiento a tu pedido, y no se comparten con terceros ajenos a la entrega.</p>
            </details>

            <label style="display:flex;gap:.6rem;align-items:flex-start;font-weight:600;margin-top:.9rem">
                <input type="checkbox" name="acepto" value="1" style="width:auto;margin-top:.35rem" required>
                <span>Acepto los términos y el aviso de privacidad *</span>
            </label>

            <div class="acciones-c">
                <button class="btn" type="submit">Realizar pedido por ${{ number_format($total, 2) }}</button>
            </div>
        </form>
    </div>
@endsection
@extends('layouts.tienda')

@section('titulo', 'Pedido ' . $venta->folio)

@section('contenido')
    <h1 class="tit">Pedido {{ $venta->folio }}</h1>
    <p class="sub">Estado: <span class="etiqueta">{{ $venta->estado }}</span> · {{ $venta->created_at->format('d/m/Y H:i') }} · {{ $venta->metodo_pago }}<br>Envío a: {{ $venta->direccion_envio }}</p>

    <div class="tarjeta tabla-wrap">
        <table>
            <thead><tr><th>Producto</th><th>Precio</th><th>Cantidad</th><th>Subtotal</th></tr></thead>
            <tbody>
                @foreach ($venta->detalles as $d)
                    <tr><td><strong>{{ $d->nombre }}</strong></td><td>${{ number_format($d->precio_unitario, 2) }}</td><td>{{ $d->cantidad }}</td><td>${{ number_format($d->subtotal, 2) }}</td></tr>
                @endforeach
                <tr><td colspan="3" style="text-align:right"><strong>Total</strong></td><td><strong>${{ number_format($venta->total, 2) }}</strong></td></tr>
            </tbody>
        </table>
    </div>

    <p style="margin-top:1.2rem"><a class="btn btn-verde" href="{{ route('tienda.pedidos') }}">← Mis pedidos</a></p>
@endsection
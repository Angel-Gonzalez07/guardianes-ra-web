@extends('layouts.tienda')

@section('titulo', 'Pedido ' . $venta->folio)

@push('estilos')
<style>
    .comp { display:grid; grid-template-columns:1.5fr 1fr; gap:1.4rem; align-items:start; }
    @media (max-width:860px) { .comp { grid-template-columns:1fr; } }
    .comp h2 { color:var(--verde); font-size:1.25rem; margin-bottom:.5rem; }
    .datos { list-style:none; margin:0; padding:0; }
    .datos li { display:flex; justify-content:space-between; gap:1rem; padding:.4rem 0; border-bottom:1px solid #EFE7CF; font-size:.95rem; }
    .datos li span:first-child { opacity:.8; flex:none; }
    .datos li span:last-child { text-align:right; font-weight:700; overflow-wrap:anywhere; }
    .linea-t { list-style:none; margin:0; padding:0 0 0 .3rem; }
    .linea-t li { position:relative; padding:0 0 1.1rem 1.6rem; border-left:3px solid var(--menta); margin-left:.4rem; font-size:.93rem; }
    .linea-t li:last-child { border-left-color:transparent; padding-bottom:0; }
    .linea-t li::before { content:''; position:absolute; left:-.55rem; top:.15rem; width:1rem; height:1rem; border-radius:50%; background:#fff; border:3px solid var(--menta); }
    .linea-t li.hecho { border-left-color:var(--verde); }
    .linea-t li.hecho::before { background:var(--verde); border-color:var(--verde); }
    .linea-t li.cancel::before { background:var(--rojo); border-color:var(--rojo); }
    .linea-t li.pend { opacity:.55; }
    .linea-t strong { display:block; }
    .linea-t small { opacity:.85; }
    .caja-pago { background:#FFF3D6; border:1px solid #F1D48A; border-radius:14px; padding:1rem 1.1rem; margin-bottom:1.2rem; }
    .caja-pago h2 { margin-bottom:.3rem; }
    .acciones-c { display:flex; flex-wrap:wrap; gap:.6rem; }
    @media print {
        .nav, footer, .no-imprimir { display:none !important; }
        body { background:#fff; }
        .tarjeta { box-shadow:none; border:1px solid #ccc; }
    }
</style>
@endpush

@section('contenido')
    <h1 class="tit">Pedido {{ $venta->folio }}</h1>
    <p class="sub">Realizado el {{ $venta->created_at->format('d/m/Y \a \l\a\s H:i') }} · Estado: <span class="etiqueta">{{ $venta->estado }}</span></p>

    @if ($venta->estado === 'pendiente')
        <div class="caja-pago">
            @if ($venta->metodo_pago === 'Transferencia')
                <h2>Realiza tu transferencia</h2>
                <ul class="datos">
                    <li><span>Banco</span><span>{{ config('tienda.banco.nombre') }}</span></li>
                    <li><span>Titular</span><span>{{ config('tienda.banco.titular') }}</span></li>
                    <li><span>Cuenta</span><span>{{ config('tienda.banco.cuenta') }}</span></li>
                    <li><span>CLABE</span><span>{{ config('tienda.banco.clabe') }}</span></li>
                    <li><span>Concepto</span><span>{{ $venta->folio }}</span></li>
                    <li><span>Importe</span><span>${{ number_format($venta->total, 2) }}</span></li>
                </ul>
                <p style="margin-top:.6rem;font-size:.9rem">Escribe tu folio en el concepto. El personal confirmará tu pago y tu pedido pasará a «pagada».</p>
            @else
                <h2>Pago contra entrega</h2>
                <p>Pagarás ${{ number_format($venta->total, 2) }} en efectivo al recibir tu pedido. El personal confirmará tu pedido antes de enviarlo.</p>
            @endif
        </div>
    @endif

    <div class="comp">
        <div>
            <div class="tarjeta tabla-wrap" style="margin-bottom:1.2rem">
                <table>
                    <thead><tr><th>Producto</th><th>Precio</th><th>Cantidad</th><th>Subtotal</th></tr></thead>
                    <tbody>
                        @foreach ($venta->detalles as $d)
                            <tr>
                                <td><strong>{{ $d->nombre }}</strong></td>
                                <td>${{ number_format($d->precio_unitario, 2) }}</td>
                                <td>{{ $d->cantidad }}</td>
                                <td>${{ number_format($d->subtotal, 2) }}</td>
                            </tr>
                        @endforeach
                        <tr><td colspan="3" style="text-align:right">Subtotal</td><td>${{ number_format($venta->subtotal, 2) }}</td></tr>
                        <tr><td colspan="3" style="text-align:right">Envío</td><td>{{ $venta->costo_envio > 0 ? '$' . number_format($venta->costo_envio, 2) : 'Gratis' }}</td></tr>
                        <tr><td colspan="3" style="text-align:right"><strong>Total</strong></td><td><strong>${{ number_format($venta->total, 2) }}</strong></td></tr>
                    </tbody>
                </table>
            </div>

            <div class="tarjeta">
                <h2>Datos de entrega</h2>
                <ul class="datos">
                    @if ($venta->destinatario)
                        <li><span>Recibe</span><span>{{ $venta->destinatario }}</span></li>
                        <li><span>Teléfono</span><span>{{ $venta->telefono }}</span></li>
                    @endif
                    <li><span>Dirección</span><span>{{ $venta->direccionCompleta() }}</span></li>
                    @if ($venta->referencias)<li><span>Referencias</span><span>{{ $venta->referencias }}</span></li>@endif
                    <li><span>Método de pago</span><span>{{ $venta->metodo_pago }}</span></li>
                </ul>
            </div>
        </div>

        <aside class="tarjeta">
            <h2>Seguimiento</h2>
            <ol class="linea-t">
                @foreach ($venta->lineaDeTiempo() as $paso)
                    @if ($venta->estado === 'cancelada' && $paso['clave'] !== 'pendiente')
                        @continue
                    @endif
                    <li class="{{ $paso['fecha'] ? 'hecho' : 'pend' }}">
                        <strong>{{ $paso['titulo'] }}</strong>
                        <small>{{ $paso['fecha'] ? $paso['fecha']->format('d/m/Y H:i') : 'Pendiente' }}
                            @if ($paso['clave'] === 'enviada' && $venta->numero_guia)<br>{{ $venta->paqueteria }} · Guía {{ $venta->numero_guia }}@endif
                        </small>
                    </li>
                @endforeach
                @if ($venta->estado === 'cancelada')
                    <li class="cancel"><strong>Pedido cancelado</strong><small>{{ $venta->cancelada_at?->format('d/m/Y H:i') }}</small></li>
                @endif
            </ol>
        </aside>
    </div>

    <div class="acciones-c no-imprimir" style="margin-top:1.4rem">
        <a class="btn btn-verde" href="{{ route('tienda.pedidos') }}">Mis pedidos</a>
        <button class="btn" type="button" onclick="window.print()">Imprimir comprobante</button>
        @if ($venta->estado === 'pendiente')
            <form method="POST" action="{{ route('tienda.cancelar', $venta) }}" onsubmit="return confirm('¿Cancelar este pedido? Las piezas regresan al inventario.');">
                @csrf
                <button class="btn btn-rojo" type="submit">Cancelar pedido</button>
            </form>
        @endif
    </div>
@endsection
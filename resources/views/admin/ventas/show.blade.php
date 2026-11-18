@extends('layouts.admin')

@section('contenido')
@include('admin._estilos')

<h1 class="adm-titulo">Pedido {{ $venta->folio }}</h1>

<div class="adm-barra">
    <a class="adm-btn sec" href="{{ route('admin.ventas.index') }}">← Ventas</a>
</div>

<div class="vta-grid">
    {{-- Datos del pedido --}}
    <section class="panel">
        <header class="panel-cab"><h2>Datos del pedido</h2></header>
        <ul class="resumen">
            <li><span>Cliente</span><strong>{{ $venta->user->name ?? '—' }}</strong></li>
            <li><span>Correo</span><strong>{{ $venta->user->email ?? '—' }}</strong></li>
            <li><span>Fecha</span><strong>{{ $venta->created_at->format('d/m/Y H:i') }}</strong></li>
            <li><span>Método de pago</span><strong>{{ $venta->metodo_pago }}</strong></li>
            <li><span>Envío a</span><strong>{{ $venta->direccion_envio }}</strong></li>
        </ul>
    </section>

    {{-- Estado y acciones --}}
    <section class="panel">
        <header class="panel-cab"><h2>Estado: {{ $venta->estado }}</h2></header>
        <div class="panel-cuerpo">
            @if (count($siguientes))
                <form method="POST" action="{{ route('admin.ventas.estado', $venta) }}" style="display:flex;gap:.5rem;flex-wrap:wrap">
                    @csrf
                    @method('PATCH')
                    @foreach ($siguientes as $s)
                        <button type="submit" name="estado" value="{{ $s }}"
                                class="adm-btn {{ $s === 'cancelada' ? 'rojo' : '' }}"
                                @if ($s === 'cancelada') onclick="return confirm('¿Cancelar el pedido? Las piezas regresan al inventario.');" @endif>
                            {{ $s === 'cancelada' ? 'Cancelar pedido' : 'Marcar como ' . $s }}
                        </button>
                    @endforeach
                </form>
            @else
                <p style="margin:0;color:var(--muted)">Este pedido ya está en un estado final ({{ $venta->estado }}).</p>
            @endif
        </div>
    </section>
</div>

{{-- Productos del pedido --}}
<section class="panel">
    <header class="panel-cab"><h2>Productos</h2></header>
    <div class="adm-tabla-wrap">
        <table class="adm-tabla">
            <thead>
                <tr><th>Producto</th><th>Precio</th><th>Cantidad</th><th>Subtotal</th></tr>
            </thead>
            <tbody>
                @foreach ($venta->detalles as $d)
                    <tr>
                        <td><strong>{{ $d->nombre }}</strong></td>
                        <td>${{ number_format($d->precio_unitario, 2) }}</td>
                        <td>{{ $d->cantidad }}</td>
                        <td>${{ number_format($d->subtotal, 2) }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="3" style="text-align:right"><strong>Total</strong></td>
                    <td><strong>${{ number_format($venta->total, 2) }}</strong></td>
                </tr>
            </tbody>
        </table>
    </div>
</section>

<style>
    .vta-grid { display: grid; grid-template-columns: 1fr 1fr; gap: .9rem; margin-bottom: .9rem; }
    @media (max-width: 800px) { .vta-grid { grid-template-columns: 1fr; } }
</style>
@endsection
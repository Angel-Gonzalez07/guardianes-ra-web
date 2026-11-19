@extends('layouts.admin')

@section('contenido')
@include('admin._estilos')

@php
    $estadoColor = [
        'borrador'  => 'background:#EEF1EC;color:#3A4A3F;border-color:#CBD6C6',
        'enviada'   => 'background:#E3F0FB;color:#0D4C85;border-color:#B9D6F0',
        'recibida'  => 'background:#E6F2E7;color:#1B5E20;border-color:#BFDCC2',
        'cancelada' => 'background:#FBE9E7;color:#A3261E;border-color:#F1C6C1',
    ];
    $etiquetas = ['enviada' => 'Marcar como enviada', 'recibida' => 'Marcar como recibida', 'cancelada' => 'Cancelar orden'];
    $confirmaciones = [
        'recibida'  => '¿Confirmas que llegó la mercancía? El stock de los productos subirá automáticamente.',
        'cancelada' => '¿Cancelar la orden de compra?',
    ];
@endphp

<h1 class="adm-titulo">Orden {{ $orden->folio }}</h1>

<div class="adm-barra">
    <a class="adm-btn sec" href="{{ route('admin.compras.index') }}">← Compras</a>
    @if ($orden->estado === 'borrador')
        <a class="adm-btn sec" href="{{ route('admin.compras.edit', $orden) }}">Editar borrador</a>
    @endif
</div>

<div class="fila iguales">
    <section class="panel">
        <header class="panel-cab"><h2>Datos de la orden</h2></header>
        <ul class="resumen">
            <li><span>Proveedor</span><strong><a href="{{ route('admin.proveedores.show', $orden->proveedor) }}">{{ $orden->proveedor->nombre }}</a></strong></li>
            <li><span>Creada por</span><strong>{{ $orden->user->name ?? '—' }}</strong></li>
            <li><span>Creada</span><strong>{{ $orden->created_at->format('d/m/Y H:i') }}</strong></li>
            <li><span>Enviada</span><strong>{{ $orden->enviada_at?->format('d/m/Y H:i') ?? '—' }}</strong></li>
            <li><span>Recibida</span><strong>{{ $orden->recibida_at?->format('d/m/Y H:i') ?? '—' }}</strong></li>
            @if ($orden->cancelada_at)
                <li><span>Cancelada</span><strong>{{ $orden->cancelada_at->format('d/m/Y H:i') }}</strong></li>
            @endif
            @if ($orden->notas)
                <li><span>Notas</span><strong style="text-align:right">{{ $orden->notas }}</strong></li>
            @endif
        </ul>
    </section>

    <section class="panel">
        <header class="panel-cab">
            <h2>Estado</h2>
            <span class="adm-badge" @style([$estadoColor[$o->estado] ?? ''])>{{ $orden->estado }}</span>
        </header>
        <div class="panel-cuerpo">
            @if (count($siguientes))
                <div style="display:flex;gap:.5rem;flex-wrap:wrap">
                    @foreach ($siguientes as $s)
                        <form method="POST" action="{{ route('admin.compras.estado', $orden) }}"
                              @if (isset($confirmaciones[$s])) onsubmit="return confirm('{{ $confirmaciones[$s] }}');" @endif>
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="estado" value="{{ $s }}">
                            <button type="submit" class="adm-btn {{ $s === 'cancelada' ? 'rojo' : '' }}">{{ $etiquetas[$s] ?? $s }}</button>
                        </form>
                    @endforeach
                </div>
                @if ($orden->estado === 'enviada')
                    <p style="margin:.8rem 0 0;color:var(--muted);font-size:.9rem">Al marcarla como recibida, las piezas se suman al inventario y queda registrado en el historial de movimientos.</p>
                @endif
            @else
                <p style="margin:0;color:var(--muted)">Esta orden ya está en un estado final ({{ $orden->estado }}).</p>
            @endif
        </div>
    </section>
</div>

<section class="panel">
    <header class="panel-cab"><h2>Productos</h2></header>
    <div class="adm-tabla-wrap">
        <table class="adm-tabla">
            <thead><tr><th>Producto</th><th>Cantidad</th><th>Costo unitario</th><th>Subtotal</th></tr></thead>
            <tbody>
                @foreach ($orden->detalles as $d)
                    <tr>
                        <td><strong>{{ $d->nombre }}</strong></td>
                        <td>{{ $d->cantidad }}</td>
                        <td>${{ number_format($d->costo_unitario, 2) }}</td>
                        <td>${{ number_format($d->subtotal, 2) }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="3" style="text-align:right"><strong>Total</strong></td>
                    <td><strong>${{ number_format($orden->total, 2) }}</strong></td>
                </tr>
            </tbody>
        </table>
    </div>
</section>
@endsection

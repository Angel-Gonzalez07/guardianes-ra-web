@extends('layouts.admin')

@section('contenido')
@include('admin._estilos')

@php
    $esAdmin = auth()->user()->esAdmin();
    $estadoColor = [
        'borrador'  => 'background:#EEF1EC;color:#3A4A3F;border-color:#CBD6C6',
        'enviada'   => 'background:#E3F0FB;color:#0D4C85;border-color:#B9D6F0',
        'recibida'  => 'background:#E6F2E7;color:#1B5E20;border-color:#BFDCC2',
        'cancelada' => 'background:#FBE9E7;color:#A3261E;border-color:#F1C6C1',
    ];
@endphp

<h1 class="adm-titulo">{{ $proveedor->nombre }}
    <span class="adm-badge {{ $proveedor->activo ? 'si' : 'no' }}" style="vertical-align:middle;font-size:.75rem">{{ $proveedor->activo ? 'Activo' : 'Inactivo' }}</span>
</h1>

<div class="adm-barra">
    <a class="adm-btn sec" href="{{ route('admin.proveedores.index') }}">← Proveedores</a>
    <div>
        @if ($proveedor->activo)
            <a class="adm-btn" href="{{ route('admin.compras.create', ['proveedor' => $proveedor->id]) }}">+ Nueva orden de compra</a>
        @endif
        @if ($esAdmin)
            <a class="adm-btn sec" href="{{ route('admin.proveedores.edit', $proveedor) }}">Editar</a>
        @endif
    </div>
</div>

<div class="kpis">
    <div class="kpi"><span class="kpi-cab">Productos que surte</span><span class="kpi-num">{{ $resumen['productos'] }}</span><span class="kpi-pie">{{ $resumen['unidades'] }} unidades en inventario</span></div>
    <div class="kpi"><span class="kpi-cab">Valor en inventario</span><span class="kpi-num">${{ number_format($resumen['valor'], 2) }}</span><span class="kpi-pie">a precio de venta</span></div>
    <div class="kpi">
        <span class="kpi-cab">Última compra recibida</span>
        @if ($ultimaCompra)
            <span class="kpi-num" style="font-size:1.5rem">{{ $ultimaCompra->recibida_at->format('d/m/Y') }}</span>
            <span class="kpi-pie"><a href="{{ route('admin.compras.show', $ultimaCompra) }}">{{ $ultimaCompra->folio }}</a> · ${{ number_format($ultimaCompra->total, 2) }}</span>
        @else
            <span class="kpi-num" style="font-size:1.5rem">—</span>
            <span class="kpi-pie">Aún sin compras recibidas</span>
        @endif
    </div>
    <div class="kpi"><span class="kpi-cab">Órdenes en curso</span><span class="kpi-num">{{ $resumen['pendientes'] }}</span><span class="kpi-pie">borrador o enviadas</span></div>
</div>

<div class="fila iguales">
    <section class="panel">
        <header class="panel-cab"><h2>Datos del proveedor</h2></header>
        <ul class="resumen">
            <li><span>RFC</span><strong>{{ $proveedor->rfc ?? '—' }}</strong></li>
            <li><span>Contacto</span><strong>{{ $proveedor->contacto }}</strong></li>
            <li><span>Correo</span><strong>{{ $proveedor->email }}</strong></li>
            <li><span>Teléfono</span><strong>{{ $proveedor->telefono }}</strong></li>
            <li><span>Ubicación</span><strong>{{ $proveedor->ciudad ? $proveedor->ciudad . ', ' . $proveedor->estado_republica : '—' }}</strong></li>
            <li><span>Dirección</span><strong>{{ $proveedor->direccion ?? '—' }}</strong></li>
            <li><span>Días de entrega</span><strong>{{ $proveedor->dias_entrega ? $proveedor->dias_entrega . ' día(s)' : '—' }}</strong></li>
            <li><span>Condiciones de pago</span><strong>{{ $proveedor->condiciones_pago ?? '—' }}</strong></li>
        </ul>
        @if ($proveedor->notas)
            <div class="panel-cuerpo" style="border-top:1px solid var(--borde)"><strong>Notas:</strong> {{ $proveedor->notas }}</div>
        @endif
    </section>

    <section class="panel">
        <header class="panel-cab"><h2>Últimas órdenes de compra</h2><a href="{{ route('admin.compras.index', ['proveedor' => $proveedor->id]) }}">Ver todas</a></header>
        <div class="adm-tabla-wrap">
            <table class="adm-tabla">
                <thead><tr><th>Folio</th><th>Fecha</th><th>Total</th><th>Estado</th></tr></thead>
                <tbody>
                    @forelse ($ordenes as $o)
                        <tr>
                            <td><a href="{{ route('admin.compras.show', $o) }}"><strong>{{ $o->folio }}</strong></a></td>
                            <td>{{ $o->created_at->format('d/m/Y') }}</td>
                            <td>${{ number_format($o->total, 2) }}</td>
                            <span class="adm-badge" @style([$estadoColor[$o->estado] ?? ''])>{{ $o->estado }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="adm-vacio">Todavía no hay órdenes de compra.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>

<section class="panel">
    <header class="panel-cab"><h2>Productos que surte</h2></header>
    <div class="adm-tabla-wrap">
        <table class="adm-tabla">
            <thead><tr><th>Producto</th><th>SKU</th><th>Categoría</th><th>Precio</th><th>Stock</th><th>Valor</th><th>Estado</th></tr></thead>
            <tbody>
                @forelse ($productos as $p)
                    <tr>
                        <td><strong>{{ $p->nombre }}</strong></td>
                        <td>{{ $p->sku }}</td>
                        <td>{{ $p->categoria->nombre ?? '—' }}</td>
                        <td>${{ number_format($p->precio, 2) }}</td>
                        <td>
                            {{ $p->stock }}
                            @if ($p->stock === 0)<span class="tag-stock cero">Agotado</span>
                            @elseif ($p->stock <= 5)<span class="tag-stock bajo">Bajo</span>@endif
                        </td>
                        <td>${{ number_format((float) $p->precio * $p->stock, 2) }}</td>
                        <td><span class="adm-badge {{ $p->activo ? 'si' : 'no' }}">{{ $p->activo ? 'Activo' : 'Inactivo' }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="adm-vacio">Este proveedor todavía no tiene productos asignados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection

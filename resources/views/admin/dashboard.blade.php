@extends('layouts.admin')

@section('contenido')
@include('admin._estilos')

@php
    $maxCat = max(1, (int) $porCategoria->max('total'));
    $maxGua = max(1, (int) $porGuardian->max('total'));

    $indicadores = [
        ['categorias',  'Categorías',  'admin.categorias.index',  '<path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/>'],
        ['productos',   'Productos',   'admin.productos.index',   '<path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/>'],
        ['proveedores', 'Proveedores', 'admin.proveedores.index', '<rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>'],
        ['clientes',    'Clientes',    'admin.clientes.index',    '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>'],
        ['usuarios',    'Usuarios',    'admin.usuarios.index',    '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>'],
    ];

    if (! auth()->user()->esAdmin()) {
        $indicadores = array_values(array_filter($indicadores, fn ($i) => $i[0] !== 'usuarios'));
    }

        
    // El empleado no ve el KPI de Usuarios
    if (! $esAdmin) {
        $indicadores = array_values(array_filter($indicadores, fn ($i) => $i[0] !== 'usuarios'));
    }

    $maxMes = max(1, (float) $ingresosMes->max('total'));

    $estadoColor = [
        'pendiente' => 'background:#FFF3D6;color:#8A5A00;border-color:#F3DDA0',
        'pagada'    => 'background:#E3F0FB;color:#0D4C85;border-color:#B9D6F0',
        'enviada'   => 'background:#EDE7F6;color:#4527A0;border-color:#D1C4E9',
        'entregada' => 'background:#E6F2E7;color:#1B5E20;border-color:#BFDCC2',
        'cancelada' => 'background:#FBE9E7;color:#A3261E;border-color:#F1C6C1',
    ];

@endphp



<h1 class="adm-titulo">Panel de control</h1>
<p class="dash-sub">Resumen general de la tienda de figuras coleccionables.</p>

{{-- ===== Ventas ===== --}}
<div class="kpis">
    @if ($esAdmin)
        <a class="kpi" href="{{ route('admin.ventas.index') }}">
            <span class="kpi-cab">Dinero ingresado</span>
            <span class="kpi-num">${{ number_format($ventas['ingresos'], 2) }}</span>
            <span class="kpi-pie">${{ number_format($ventas['ingresos_mes'], 2) }} este mes</span>
        </a>
    @endif
    <a class="kpi" href="{{ route('admin.ventas.index') }}">
        <span class="kpi-cab">Ventas confirmadas del mes</span>
        <span class="kpi-num">{{ $ventas['mes_pedidos'] }}</span>
        <span class="kpi-pie">{{ $ventas['pedidos'] }} pedidos en total</span>
    </a>
    <a class="kpi" href="{{ route('admin.ventas.index', ['estado' => 'pendiente']) }}">
        <span class="kpi-cab">Pedidos pendientes</span>
        <span class="kpi-num">{{ $ventas['pendientes'] }}</span>
        <span class="kpi-pie">{{ $ventas['canceladas'] }} cancelados</span>
    </a>
    @if ($esAdmin)
        <div class="kpi">
            <span class="kpi-cab">Ticket promedio</span>
            <span class="kpi-num">${{ number_format($ventas['ticket'], 2) }}</span>
            <span class="kpi-pie">por venta confirmada</span>
        </div>
        <div class="kpi">
            <span class="kpi-cab">Usuarios registrados</span>
            <span class="kpi-num">{{ $registrados['clientes'] }}</span>
            <span class="kpi-pie">{{ $registrados['nuevos'] }} nuevos este mes · {{ $registrados['personal'] }} del personal</span>
        </div>
    @endif
</div>

<div class="fila">
    @if ($esAdmin)
        <section class="panel">
            <header class="panel-cab"><h2>Ingresos de los últimos 6 meses</h2></header>
            <ul class="barras">
                @foreach ($ingresosMes as $m)
                    <li>
                        <span class="b-nom">{{ $m['mes'] }}</span>
                        <span class="b-pista"><i style="width: {{ round($m['total'] / $maxMes * 100) }}%"></i></span>
                        <span class="b-num" style="white-space:nowrap">${{ number_format($m['total'], 0) }}</span>
                    </li>
                @endforeach
            </ul>
        </section>
    @else
        <section class="panel">
            <header class="panel-cab"><h2>Últimos pedidos</h2><a href="{{ route('admin.ventas.index') }}">Ver todos</a></header>
            @include('admin._ultimas_ventas')
        </section>
    @endif

    <section class="panel">
        <header class="panel-cab"><h2>Productos más vendidos</h2></header>
        @if ($masVendidos->isEmpty())
            <p class="vacio-p">Aún no hay ventas confirmadas.</p>
        @else
            <ul class="resumen">
                @foreach ($masVendidos as $mv)
                    <li><span>{{ $mv->nombre }}</span><strong>{{ (int) $mv->unidades }} uds{{ $esAdmin ? ' · $' . number_format($mv->importe, 2) : '' }}</strong></li>
                @endforeach
            </ul>
        @endif
    </section>
</div>

@if ($esAdmin)
<div class="fila iguales">
    <section class="panel">
        <header class="panel-cab"><h2>Últimos pedidos</h2><a href="{{ route('admin.ventas.index') }}">Ver todos</a></header>
        @include('admin._ultimas_ventas')
    </section>
    <section class="panel">
        <header class="panel-cab"><h2>Clientes que más han gastado</h2></header>
        @if ($topClientes->isEmpty())
            <p class="vacio-p">Aún no hay compras confirmadas.</p>
        @else
            <ul class="resumen">
                @foreach ($topClientes as $c)
                    <li><span>{{ $c->name }} ({{ (int) $c->pedidos }} pedidos)</span><strong>${{ number_format($c->gastado, 2) }}</strong></li>
                @endforeach
            </ul>
        @endif
    </section>
</div>
@endif

<h2 class="adm-titulo" style="margin-top:1.4rem;font-size:1.05rem">Catálogos e inventario</h2>

<div class="kpis">
    @foreach ($indicadores as $i)
        <a class="kpi" href="{{ route($i[2]) }}">
            <span class="kpi-cab">{{ $i[1] }} <svg viewBox="0 0 24 24" aria-hidden="true">{!! $i[3] !!}</svg></span>
            <span class="kpi-num">{{ $totales[$i[0]]['total'] }}</span>
            <span class="kpi-pie">{{ $totales[$i[0]]['activos'] }} activos</span>
        </a>
    @endforeach
</div>

<div class="fila">
    <section class="panel">
        <header class="panel-cab">
            <h2>Últimos productos registrados</h2>
            <a href="{{ route('admin.productos.index') }}">Ver todos</a>
        </header>
        <div class="adm-tabla-wrap">
            <table class="adm-tabla">
                <thead>
                    <tr><th>Producto</th><th>SKU</th><th>Categoría</th><th>Precio</th><th>Stock</th></tr>
                </thead>
                <tbody>
                    @forelse ($recientes as $p)
                        <tr>
                            <td><strong>{{ $p->nombre }}</strong></td>
                            <td>{{ $p->sku }}</td>
                            <td>{{ $p->categoria->nombre ?? '—' }}</td>
                            <td>${{ number_format($p->precio, 2) }}</td>
                            <td>
                                {{ $p->stock }}
                                @if ($p->stock == 0)
                                    <span class="tag-stock cero">Sin stock</span>
                                @elseif ($p->stock <= 5)
                                    <span class="tag-stock bajo">Bajo</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="adm-vacio">Todavía no hay productos registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="panel">
        <header class="panel-cab"><h2>Inventario</h2></header>
        <ul class="resumen">
            <li><span>Unidades en existencia</span><strong>{{ number_format($inventario['unidades']) }}</strong></li>
            @if ($esAdmin)
                <li><span>Valor estimado del inventario</span><strong>${{ number_format($inventario['valor'], 2) }}</strong></li>
            @endif
            <li><span>Productos con stock bajo (5 o menos)</span><strong>{{ $inventario['stock_bajo'] }}</strong></li>
            <li><span>Productos sin stock</span><strong class="{{ $inventario['sin_stock'] > 0 ? 'alerta-txt' : '' }}">{{ $inventario['sin_stock'] }}</strong></li>
        </ul>
    </section>
</div>

<div class="fila iguales">
    <section class="panel">
        <header class="panel-cab"><h2>Productos por categoría</h2></header>
        @if ($porCategoria->isEmpty())
            <p class="vacio-p">Todavía no hay categorías registradas.</p>
        @else
            <ul class="barras">
                @foreach ($porCategoria as $c)
                    <li>
                        <span class="b-nom">{{ $c->nombre }}</span>
                        <span class="b-pista"><i style="width: {{ round($c->total / $maxCat * 100) }}%"></i></span>
                        <span class="b-num">{{ $c->total }}</span>
                    </li>
                @endforeach
            </ul>
        @endif
    </section>

    <section class="panel">
        <header class="panel-cab"><h2>Productos por guardián</h2></header>
        @if ($porGuardian->isEmpty())
            <p class="vacio-p">Ningún producto tiene guardián asignado todavía.</p>
        @else
            <ul class="barras">
                @foreach ($porGuardian as $g)
                    <li>
                        <span class="b-nom">{{ $g->guardian }}</span>
                        <span class="b-pista"><i style="width: {{ round($g->total / $maxGua * 100) }}%"></i></span>
                        <span class="b-num">{{ $g->total }}</span>
                    </li>
                @endforeach
            </ul>
        @endif
    </section>
</div>

<section class="panel">
    <header class="panel-cab"><h2>Accesos rápidos</h2></header>
    <div class="accesos">
        <a class="adm-btn" href="{{ route('admin.productos.create') }}">Nuevo producto</a>
        <a class="adm-btn sec" href="{{ route('admin.categorias.create') }}">Nueva categoría</a>
        <a class="adm-btn sec" href="{{ route('admin.clientes.create') }}">Nuevo cliente</a>
        <a class="adm-btn sec" href="{{ route('admin.ventas.index') }}">Ver ventas</a>
        @if ($esAdmin)
            <a class="adm-btn sec" href="{{ route('admin.proveedores.create') }}">Nuevo proveedor</a>
            <a class="adm-btn sec" href="{{ route('admin.usuarios.create') }}">Nuevo usuario</a>
        @endif
    </div>
</section>
@endsection
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
    $hayFiltro = $q !== '' || $estado !== '' || $proveedorId > 0;
@endphp

<h1 class="adm-titulo">Compras a proveedores</h1>

<div class="adm-barra">
    <form class="adm-buscar" method="GET" action="{{ route('admin.compras.index') }}">
        <input type="text" name="q" value="{{ $q }}" placeholder="Buscar por folio" maxlength="30">
        <select name="estado" style="padding:.55rem .6rem;border:1px solid var(--borde);border-radius:8px;font:inherit;background:#fff">
            <option value="">Todos los estados</option>
            @foreach ($estados as $e)
                <option value="{{ $e }}" @selected($estado === $e)>{{ ucfirst($e) }}</option>
            @endforeach
        </select>
        <select name="proveedor" style="padding:.55rem .6rem;border:1px solid var(--borde);border-radius:8px;font:inherit;background:#fff;max-width:220px">
            <option value="">Todos los proveedores</option>
            @foreach ($proveedores as $p)
                <option value="{{ $p->id }}" @selected($proveedorId === $p->id)>{{ $p->nombre }}</option>
            @endforeach
        </select>
        <input type="hidden" name="dir" value="{{ $dir }}">
        <button class="adm-btn" type="submit">Buscar</button>
        @if ($hayFiltro)
            <a class="adm-btn sec" href="{{ route('admin.compras.index') }}">Limpiar</a>
        @endif
    </form>
    <div>
        <a class="adm-btn sec" href="{{ route('admin.dashboard') }}">← Panel</a>
        <a class="adm-btn" href="{{ route('admin.compras.create') }}">+ Nueva orden de compra</a>
    </div>
</div>

<div class="adm-tabla-wrap">
    <table class="adm-tabla">
        <thead>
            <tr>
                <th>Folio</th>
                <th>
                    <a href="{{ route('admin.compras.index', ['q' => $q, 'estado' => $estado, 'proveedor' => $proveedorId ?: null, 'dir' => $dir === 'asc' ? 'desc' : 'asc']) }}">
                        Fecha {{ $dir === 'asc' ? '▲' : '▼' }}
                    </a>
                </th>
                <th>Proveedor</th>
                <th>Productos</th>
                <th>Total</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($ordenes as $o)
                <tr>
                    <td><strong>{{ $o->folio }}</strong></td>
                    <td>{{ $o->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $o->proveedor->nombre ?? '—' }}</td>
                    <td>{{ $o->detalles_count }} línea(s)</td>
                    <td>${{ number_format($o->total, 2) }}</td>
                    <td><span class="adm-badge" style="{{ $estadoColor[$o->estado] ?? '' }}">{{ $o->estado }}</span></td>
                    <td><a class="adm-btn chico sec" href="{{ route('admin.compras.show', $o) }}">Ver detalle</a></td>
                </tr>
            @empty
                <tr><td colspan="7" class="adm-vacio">No hay órdenes de compra{{ $hayFiltro ? ' que coincidan con el filtro' : ' todavía' }}.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($ordenes->hasPages())
    <div class="adm-pag">
        @if ($ordenes->previousPageUrl())
            <a class="adm-btn chico sec" href="{{ $ordenes->previousPageUrl() }}">← Anterior</a>
        @endif
        <span>Página {{ $ordenes->currentPage() }} de {{ $ordenes->lastPage() }}</span>
        @if ($ordenes->nextPageUrl())
            <a class="adm-btn chico sec" href="{{ $ordenes->nextPageUrl() }}">Siguiente →</a>
        @endif
    </div>
@endif
@endsection

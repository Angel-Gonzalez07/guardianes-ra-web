@extends('layouts.admin')

@section('contenido')
@include('admin._estilos')

@php
    // Color de la etiqueta según el estado del pedido
    $estadoColor = [
        'pendiente' => 'background:#FFF3D6;color:#8A5A00;border-color:#F3DDA0',
        'pagada'    => 'background:#E3F0FB;color:#0D4C85;border-color:#B9D6F0',
        'enviada'   => 'background:#EDE7F6;color:#4527A0;border-color:#D1C4E9',
        'entregada' => 'background:#E6F2E7;color:#1B5E20;border-color:#BFDCC2',
        'cancelada' => 'background:#FBE9E7;color:#A3261E;border-color:#F1C6C1',
    ];
@endphp

<h1 class="adm-titulo">Ventas</h1>

<div class="adm-barra">
    <form class="adm-buscar" method="GET" action="{{ route('admin.ventas.index') }}">
        <input type="text" name="q" value="{{ $q }}" placeholder="Buscar por folio, cliente o correo" maxlength="80">
        <select name="estado" style="padding:.5rem .6rem;border:1px solid #C5CEC8;border-radius:8px;font:inherit;background:#fff">
            <option value="">Todos los estados</option>
            @foreach ($estados as $e)
                <option value="{{ $e }}" @selected($estado === $e)>{{ ucfirst($e) }}</option>
            @endforeach
        </select>
        <input type="hidden" name="dir" value="{{ $dir }}">
        <button class="adm-btn" type="submit">Buscar</button>
        @if ($q !== '' || $estado !== '')
            <a class="adm-btn sec" href="{{ route('admin.ventas.index') }}">Limpiar</a>
        @endif
    </form>
    <a class="adm-btn sec" href="{{ route('admin.dashboard') }}">← Panel</a>
</div>

<div class="adm-tabla-wrap">
    <table class="adm-tabla">
        <thead>
            <tr>
                <th>Folio</th>
                <th>
                    <a href="{{ route('admin.ventas.index', ['q' => $q, 'estado' => $estado, 'dir' => $dir === 'asc' ? 'desc' : 'asc']) }}">
                        Fecha {{ $dir === 'asc' ? '▲' : '▼' }}
                    </a>
                </th>
                <th>Cliente</th>
                <th>Productos</th>
                <th>Total</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($ventas as $v)
                <tr>
                    <td><strong>{{ $v->folio }}</strong></td>
                    <td>{{ $v->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        {{ $v->user->name ?? '—' }}<br>
                        <small style="color:var(--muted)">{{ $v->user->email ?? '' }}</small>
                    </td>
                    <td>{{ $v->detalles_count }} línea(s)</td>
                    <td>${{ number_format($v->total, 2) }}</td>
                    <td><span class="adm-badge" style="{{ $estadoColor[$v->estado] ?? '' }}">{{ $v->estado }}</span></td>
                    <td><a class="adm-btn chico sec" href="{{ route('admin.ventas.show', $v) }}">Ver detalle</a></td>
                </tr>
            @empty
                <tr><td colspan="7" class="adm-vacio">No hay ventas{{ ($q !== '' || $estado !== '') ? ' que coincidan con el filtro' : ' registradas todavía' }}.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($ventas->hasPages())
    <div class="adm-pag">
        @if ($ventas->previousPageUrl())
            <a class="adm-btn chico sec" href="{{ $ventas->previousPageUrl() }}">← Anterior</a>
        @endif
        <span>Página {{ $ventas->currentPage() }} de {{ $ventas->lastPage() }}</span>
        @if ($ventas->nextPageUrl())
            <a class="adm-btn chico sec" href="{{ $ventas->nextPageUrl() }}">Siguiente →</a>
        @endif
    </div>
@endif
@endsection
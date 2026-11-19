s@extends('layouts.admin')

@section('contenido')
@include('admin._estilos')

@php $hayFiltro = $q !== '' || $tipo !== '' || $motivo !== ''; @endphp

<h1 class="adm-titulo">Historial de inventario</h1>

<div class="adm-barra">
    <form class="adm-buscar" method="GET" action="{{ route('admin.inventario.index') }}">
        <input type="text" name="q" value="{{ $q }}" placeholder="Buscar por producto, SKU o folio" maxlength="60">
        <select name="tipo" style="padding:.55rem .6rem;border:1px solid var(--borde);border-radius:8px;font:inherit;background:#fff">
            <option value="">Entradas y salidas</option>
            <option value="entrada" @selected($tipo === 'entrada')>Solo entradas</option>
            <option value="salida" @selected($tipo === 'salida')>Solo salidas</option>
        </select>
        <select name="motivo" style="padding:.55rem .6rem;border:1px solid var(--borde);border-radius:8px;font:inherit;background:#fff">
            <option value="">Todos los motivos</option>
            @foreach ($motivos as $clave => $texto)
                <option value="{{ $clave }}" @selected($motivo === $clave)>{{ $texto }}</option>
            @endforeach
        </select>
        <input type="hidden" name="dir" value="{{ $dir }}">
        <button class="adm-btn" type="submit">Buscar</button>
        @if ($hayFiltro)
            <a class="adm-btn sec" href="{{ route('admin.inventario.index') }}">Limpiar</a>
        @endif
    </form>
    <a class="adm-btn sec" href="{{ route('admin.dashboard') }}">← Panel</a>
</div>

<div class="adm-tabla-wrap">
    <table class="adm-tabla">
        <thead>
            <tr>
                <th>
                    <a href="{{ route('admin.inventario.index', ['q' => $q, 'tipo' => $tipo, 'motivo' => $motivo, 'dir' => $dir === 'asc' ? 'desc' : 'asc']) }}">
                        Fecha {{ $dir === 'asc' ? '▲' : '▼' }}
                    </a>
                </th>
                <th>Producto</th>
                <th>Movimiento</th>
                <th>Cantidad</th>
                <th>Stock resultante</th>
                <th>Motivo</th>
                <th>Referencia</th>
                <th>Usuario</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($movimientos as $m)
                <tr>
                    <td>{{ $m->created_at->format('d/m/Y H:i') }}</td>
                    <td><strong>{{ $m->producto->nombre ?? '—' }}</strong><br><small style="color:var(--muted)">{{ $m->producto->sku ?? '' }}</small></td>
                    <td><span class="adm-badge {{ $m->tipo === 'entrada' ? 'si' : 'no' }}">{{ $m->tipo === 'entrada' ? 'Entrada' : 'Salida' }}</span></td>
                    <td><strong>{{ $m->tipo === 'entrada' ? '+' : '−' }}{{ $m->cantidad }}</strong></td>
                    <td>{{ $m->stock_resultante }}</td>
                    <td>{{ $motivos[$m->motivo] ?? $m->motivo }}</td>
                    <td>{{ $m->referencia ?? '—' }}</td>
                    <td>{{ $m->user->name ?? 'Sistema' }}</td>
                </tr>
            @empty
                <tr><td colspan="8" class="adm-vacio">No hay movimientos{{ $hayFiltro ? ' que coincidan con el filtro' : ' registrados todavía' }}.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($movimientos->hasPages())
    <div class="adm-pag">
        @if ($movimientos->previousPageUrl())
            <a class="adm-btn chico sec" href="{{ $movimientos->previousPageUrl() }}">← Anterior</a>
        @endif
        <span>Página {{ $movimientos->currentPage() }} de {{ $movimientos->lastPage() }}</span>
        @if ($movimientos->nextPageUrl())
            <a class="adm-btn chico sec" href="{{ $movimientos->nextPageUrl() }}">Siguiente →</a>
        @endif
    </div>
@endif
@endsection

@extends('layouts.admin')

@section('contenido')
@include('admin._estilos')

@php
    $esAdmin = auth()->user()->esAdmin();

    // Enlace de cada encabezado: conserva búsqueda y filtro, y alterna el sentido del orden
    $enlace = fn ($col) => route('admin.proveedores.index', [
        'q' => $q, 'estado' => $estado, 'orden' => $col,
        'dir' => ($orden === $col && $dir === 'asc') ? 'desc' : 'asc',
    ]);
    $flecha = fn ($col) => $orden === $col ? ($dir === 'asc' ? ' ▲' : ' ▼') : '';
@endphp

<h1 class="adm-titulo">Proveedores</h1>

<div class="adm-barra">
    <form class="adm-buscar" method="GET" action="{{ route('admin.proveedores.index') }}">
        <input type="text" name="q" value="{{ $q }}" placeholder="Buscar por nombre, RFC, contacto, ciudad..." maxlength="80">
        <select name="estado" style="padding:.55rem .6rem;border:1px solid var(--borde);border-radius:8px;font:inherit;background:#fff">
            <option value="">Todos</option>
            <option value="activos" @selected($estado === 'activos')>Solo activos</option>
            <option value="inactivos" @selected($estado === 'inactivos')>Solo inactivos</option>
        </select>
        <input type="hidden" name="orden" value="{{ $orden }}">
        <input type="hidden" name="dir" value="{{ $dir }}">
        <button class="adm-btn" type="submit">Buscar</button>
        @if ($q !== '' || $estado !== '')
            <a class="adm-btn sec" href="{{ route('admin.proveedores.index') }}">Limpiar</a>
        @endif
    </form>
    <div>
        <a class="adm-btn sec" href="{{ route('admin.proveedores.exportar', ['q' => $q, 'estado' => $estado, 'orden' => $orden, 'dir' => $dir]) }}">Exportar CSV</a>
        @if ($esAdmin)
            <a class="adm-btn" href="{{ route('admin.proveedores.create') }}">+ Nuevo proveedor</a>
        @endif
    </div>
</div>

<div class="adm-tabla-wrap">
    <table class="adm-tabla">
        <thead>
            <tr>
                <th><a href="{{ $enlace('nombre') }}">Nombre{{ $flecha('nombre') }}</a></th>
                <th>RFC</th>
                <th><a href="{{ $enlace('ciudad') }}">Ciudad{{ $flecha('ciudad') }}</a></th>
                <th><a href="{{ $enlace('dias_entrega') }}">Entrega{{ $flecha('dias_entrega') }}</a></th>
                <th>Productos</th>
                <th><a href="{{ $enlace('activo') }}">Estado{{ $flecha('activo') }}</a></th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($proveedores as $p)
                <tr>
                    <td>
                        <a href="{{ route('admin.proveedores.show', $p) }}"><strong>{{ $p->nombre }}</strong></a><br>
                        <small style="color:var(--muted)">{{ $p->contacto }} · {{ $p->telefono }}</small>
                    </td>
                    <td>{{ $p->rfc ?? '—' }}</td>
                    <td>{{ $p->ciudad ? $p->ciudad . ', ' . $p->estado_republica : '—' }}</td>
                    <td>{{ $p->dias_entrega ? $p->dias_entrega . ' día(s)' : '—' }}</td>
                    <td>{{ $p->productos_count }}</td>
                    <td><span class="adm-badge {{ $p->activo ? 'si' : 'no' }}">{{ $p->activo ? 'Activo' : 'Inactivo' }}</span></td>
                    <td>
                        <div class="adm-acciones">
                            <a class="adm-btn chico sec" href="{{ route('admin.proveedores.show', $p) }}">Ficha</a>
                            @if ($esAdmin)
                                <a class="adm-btn chico sec" href="{{ route('admin.proveedores.edit', $p) }}">Editar</a>
                                <form method="POST" action="{{ route('admin.proveedores.destroy', $p) }}"
                                      onsubmit="return confirm('¿Eliminar al proveedor {{ addslashes($p->nombre) }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="adm-btn chico rojo" type="submit">Eliminar</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="adm-vacio">No hay proveedores{{ ($q !== '' || $estado !== '') ? ' que coincidan con el filtro' : '' }}.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($proveedores->hasPages())
    <div class="adm-pag">
        @if ($proveedores->previousPageUrl())
            <a class="adm-btn chico sec" href="{{ $proveedores->previousPageUrl() }}">← Anterior</a>
        @endif
        <span>Página {{ $proveedores->currentPage() }} de {{ $proveedores->lastPage() }}</span>
        @if ($proveedores->nextPageUrl())
            <a class="adm-btn chico sec" href="{{ $proveedores->nextPageUrl() }}">Siguiente →</a>
        @endif
    </div>
@endif
@endsection

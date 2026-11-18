@extends('layouts.admin')

@section('contenido')
@include('admin._estilos')

<h1 class="adm-titulo">Clientes</h1>

<div class="adm-barra">
    <form class="adm-buscar" method="GET" action="{{ route('admin.clientes.index') }}">
        <input type="text" name="q" value="{{ $q }}" placeholder="Buscar por nombre, teléfono, ciudad, estado, CP o correo" maxlength="80" style="min-width:300px">
        <input type="hidden" name="dir" value="{{ $dir }}">
        <button class="adm-btn" type="submit">Buscar</button>
        @if ($q !== '')
            <a class="adm-btn sec" href="{{ route('admin.clientes.index') }}">Limpiar</a>
        @endif
    </form>
    <div>
        <a class="adm-btn sec" href="{{ route('admin.dashboard') }}">← Panel</a>
        <a class="adm-btn" href="{{ route('admin.clientes.create') }}">+ Nuevo cliente</a>
    </div>
</div>

<div class="adm-tabla-wrap">
    <table class="adm-tabla">
        <thead>
            <tr>
                <th>
                    <a href="{{ route('admin.clientes.index', ['q' => $q, 'dir' => $dir === 'asc' ? 'desc' : 'asc']) }}">
                        Cliente {{ $dir === 'asc' ? '▲' : '▼' }}
                    </a>
                </th>
                <th>Cuenta</th>
                <th>Teléfono</th>
                <th>Ciudad</th>
                <th>Estado</th>
                <th>CP</th>
                <th>Activo</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($clientes as $c)
                <tr>
                    <td><strong>{{ $c->apellidos }}, {{ $c->nombre }}</strong></td>
                    <td>{{ $c->user->email ?? 'Sin cuenta' }}</td>
                    <td>{{ $c->telefono }}</td>
                    <td>{{ $c->ciudad }}</td>
                    <td>{{ $c->estado }}</td>
                    <td>{{ $c->codigo_postal }}</td>
                    <td>
                        <span class="adm-badge {{ $c->activo ? 'si' : 'no' }}">{{ $c->activo ? 'Sí' : 'No' }}</span>
                    </td>
                    <td>
                        <div class="adm-acciones">
                            <a class="adm-btn chico sec" href="{{ route('admin.clientes.edit', $c) }}">Editar</a>
                            @if (auth()->user()->esAdmin())
                            <form method="POST" action="{{ route('admin.clientes.destroy', $c) }}"
                                  onsubmit="return confirm('¿Eliminar al cliente {{ addslashes($c->nombre . ' ' . $c->apellidos) }}?');">
                                @csrf
                                @method('DELETE')
                                <button class="adm-btn chico rojo" type="submit">Eliminar</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="adm-vacio">No hay clientes{{ $q !== '' ? ' que coincidan con la búsqueda' : '' }}.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($clientes->hasPages())
    <div class="adm-pag">
        @if ($clientes->previousPageUrl())
            <a class="adm-btn chico sec" href="{{ $clientes->previousPageUrl() }}">← Anterior</a>
        @endif
        <span>Página {{ $clientes->currentPage() }} de {{ $clientes->lastPage() }}</span>
        @if ($clientes->nextPageUrl())
            <a class="adm-btn chico sec" href="{{ $clientes->nextPageUrl() }}">Siguiente →</a>
        @endif
    </div>
@endif
@endsection
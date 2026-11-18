@extends('layouts.admin')

@section('contenido')
@include('admin._estilos')

<h1 class="adm-titulo">Usuarios</h1>

<div class="adm-barra">
    <form class="adm-buscar" method="GET" action="{{ route('admin.usuarios.index') }}">
        <input type="text" name="q" value="{{ $q }}" placeholder="Buscar por nombre, correo o rol" maxlength="80">
        <input type="hidden" name="dir" value="{{ $dir }}">
        <button class="adm-btn" type="submit">Buscar</button>
        @if ($q !== '')
            <a class="adm-btn sec" href="{{ route('admin.usuarios.index') }}">Limpiar</a>
        @endif
    </form>
    <div>
        <a class="adm-btn sec" href="{{ route('admin.dashboard') }}">← Panel</a>
        <a class="adm-btn" href="{{ route('admin.usuarios.create') }}">+ Nuevo usuario</a>
    </div>
</div>

<div class="adm-tabla-wrap">
    <table class="adm-tabla">
        <thead>
            <tr>
                <th>
                    <a href="{{ route('admin.usuarios.index', ['q' => $q, 'dir' => $dir === 'asc' ? 'desc' : 'asc']) }}">
                        Nombre {{ $dir === 'asc' ? '▲' : '▼' }}
                    </a>
                </th>
                <th>Correo</th>
                <th>Rol</th>
                <th>Activo</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($usuarios as $u)
                <tr>
                    <td>
                        <strong>{{ $u->name }}</strong>
                        @if ($u->id === auth()->id()) <small>(tú)</small> @endif
                    </td>
                    <td>{{ $u->email }}</td>
                    <td>
                        <span class="adm-badge {{ ($u->role->nombre ?? '') === 'admin' ? 'si' : 'no' }}"
                              style="{{ ($u->role->nombre ?? '') === 'admin' ? '' : 'background:#FFE0B2;color:#8D5A00' }}">
                            {{ $u->role->nombre ?? '—' }}
                        </span>
                    </td>
                    <td>
                        <span class="adm-badge {{ $u->activo ? 'si' : 'no' }}">{{ $u->activo ? 'Sí' : 'No' }}</span>
                    </td>
                    <td>
                        <div class="adm-acciones">
                            <a class="adm-btn chico sec" href="{{ route('admin.usuarios.edit', $u) }}">Editar</a>
                            @if ($u->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.usuarios.destroy', $u) }}"
                                      onsubmit="return confirm('¿Eliminar al usuario {{ addslashes($u->name) }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="adm-btn chico rojo" type="submit">Eliminar</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="adm-vacio">No hay usuarios{{ $q !== '' ? ' que coincidan con la búsqueda' : '' }}.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($usuarios->hasPages())
    <div class="adm-pag">
        @if ($usuarios->previousPageUrl())
            <a class="adm-btn chico sec" href="{{ $usuarios->previousPageUrl() }}">← Anterior</a>
        @endif
        <span>Página {{ $usuarios->currentPage() }} de {{ $usuarios->lastPage() }}</span>
        @if ($usuarios->nextPageUrl())
            <a class="adm-btn chico sec" href="{{ $usuarios->nextPageUrl() }}">Siguiente →</a>
        @endif
    </div>
@endif
@endsection

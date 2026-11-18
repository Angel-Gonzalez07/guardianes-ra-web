@extends('layouts.admin')

@section('contenido')
@include('admin._estilos')

@php $esAdmin = auth()->user()->esAdmin(); @endphp

<h1 class="adm-titulo">Proveedores</h1>

<div class="adm-barra">
    <form class="adm-buscar" method="GET" action="{{ route('admin.proveedores.index') }}">
        <input type="text" name="q" value="{{ $q }}" placeholder="Buscar por nombre, contacto, correo o teléfono" maxlength="80">
        <input type="hidden" name="dir" value="{{ $dir }}">
        <button class="adm-btn" type="submit">Buscar</button>
        @if ($q !== '')
            <a class="adm-btn sec" href="{{ route('admin.proveedores.index') }}">Limpiar</a>
        @endif
    </form>
    <div>
        <a class="adm-btn sec" href="{{ route('admin.dashboard') }}">← Panel</a>
        @if ($esAdmin)
            <a class="adm-btn" href="{{ route('admin.proveedores.create') }}">+ Nuevo proveedor</a>
        @endif
    </div>
</div>

<div class="adm-tabla-wrap">
    <table class="adm-tabla">
        <thead>
            <tr>
                <th>
                    <a href="{{ route('admin.proveedores.index', ['q' => $q, 'dir' => $dir === 'asc' ? 'desc' : 'asc']) }}">
                        Nombre {{ $dir === 'asc' ? '▲' : '▼' }}
                    </a>
                </th>
                <th>Contacto</th>
                <th>Correo</th>
                <th>Teléfono</th>
                <th>Estado</th>
                @if (auth()->user()->esAdmin())<th>Acciones</th>@endif
                <tr>
            </tr>
        </thead>
        <tbody>
            @forelse ($proveedores as $p)
                <tr>
                    <td><strong>{{ $p->nombre }}</strong></td>
                    <td>{{ $p->contacto }}</td>
                    <td>{{ $p->email }}</td>
                    <td>{{ $p->telefono }}</td>
                    <td>
                        <span class="adm-badge {{ $p->activo ? 'si' : 'no' }}">{{ $p->activo ? 'Activo' : 'Inactivo' }}</span>
                    </td>
                    @if ($esAdmin)
                        <td>
                            <div class="adm-acciones">
                                <a class="adm-btn chico sec" href="{{ route('admin.proveedores.edit', $p) }}">Editar</a>
                                <form method="POST" action="{{ route('admin.proveedores.destroy', $p) }}"
                                      onsubmit="return confirm('¿Eliminar al proveedor {{ addslashes($p->nombre) }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="adm-btn chico rojo" type="submit">Eliminar</button>
                                </form>
                            </div>
                        </td>
                    @endif
                </tr>
            @empty
                <tr><td colspan="{{ $esAdmin ? 6 : 5 }}" class="adm-vacio">No hay proveedores{{ $q !== '' ? ' que coincidan con la búsqueda' : '' }}.</td></tr>
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
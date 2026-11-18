@extends('layouts.admin')

@section('contenido')
@include('admin._estilos')

<h1 class="adm-titulo">Categorías</h1>

<div class="adm-barra">
    <form class="adm-buscar" method="GET" action="{{ route('admin.categorias.index') }}">
        <input type="text" name="q" value="{{ $q }}" placeholder="Buscar por nombre o descripción" maxlength="80">
        <input type="hidden" name="dir" value="{{ $dir }}">
        <button class="adm-btn" type="submit">Buscar</button>
        @if ($q !== '')
            <a class="adm-btn sec" href="{{ route('admin.categorias.index') }}">Limpiar</a>
        @endif
    </form>
    <div>
        <a class="adm-btn sec" href="{{ route('admin.dashboard') }}">← Panel</a>
        <a class="adm-btn" href="{{ route('admin.categorias.create') }}">+ Nueva categoría</a>
    </div>
</div>

<div class="adm-tabla-wrap">
    <table class="adm-tabla">
        <thead>
            <tr>
                <th>
                    <a href="{{ route('admin.categorias.index', ['q' => $q, 'dir' => $dir === 'asc' ? 'desc' : 'asc']) }}">
                        Nombre {{ $dir === 'asc' ? '▲' : '▼' }}
                    </a>
                </th>
                <th>Descripción</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($categorias as $c)
                <tr>
                    <td><strong>{{ $c->nombre }}</strong></td>
                    <td>{{ $c->descripcion ?? '—' }}</td>
                    <td>
                        <span class="adm-badge {{ $c->activo ? 'si' : 'no' }}">{{ $c->activo ? 'Activa' : 'Inactiva' }}</span>
                    </td>
                    <td>
                        <div class="adm-acciones">
                            <a class="adm-btn chico sec" href="{{ route('admin.categorias.edit', $c) }}">Editar</a>
                            @if (auth()->user()->esAdmin())
                            <form method="POST" action="{{ route('admin.categorias.destroy', $c) }}"
                                  onsubmit="return confirm('¿Eliminar la categoría {{ addslashes($c->nombre) }}?');">
                                @csrf
                                @method('DELETE')
                                <button class="adm-btn chico rojo" type="submit">Eliminar</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="adm-vacio">No hay categorías{{ $q !== '' ? ' que coincidan con la búsqueda' : '' }}.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($categorias->hasPages())
    <div class="adm-pag">
        @if ($categorias->previousPageUrl())
            <a class="adm-btn chico sec" href="{{ $categorias->previousPageUrl() }}">← Anterior</a>
        @endif
        <span>Página {{ $categorias->currentPage() }} de {{ $categorias->lastPage() }}</span>
        @if ($categorias->nextPageUrl())
            <a class="adm-btn chico sec" href="{{ $categorias->nextPageUrl() }}">Siguiente →</a>
        @endif
    </div>
@endif
@endsection
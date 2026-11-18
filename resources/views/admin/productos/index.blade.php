@extends('layouts.admin')

@section('contenido')
@include('admin._estilos')

<h1 class="adm-titulo">Productos</h1>

<div class="adm-barra">
    <form class="adm-buscar" method="GET" action="{{ route('admin.productos.index') }}">
        <input type="text" name="q" value="{{ $q }}" placeholder="Buscar por nombre, SKU, guardián, categoría o proveedor" maxlength="80" style="min-width:300px">
        <input type="hidden" name="dir" value="{{ $dir }}">
        <button class="adm-btn" type="submit">Buscar</button>
        @if ($q !== '')
            <a class="adm-btn sec" href="{{ route('admin.productos.index') }}">Limpiar</a>
        @endif
    </form>
    <div>
        <a class="adm-btn sec" href="{{ route('admin.dashboard') }}">← Panel</a>
        <a class="adm-btn" href="{{ route('admin.productos.create') }}">+ Nuevo producto</a>
    </div>
</div>

<div class="adm-tabla-wrap">
    <table class="adm-tabla">
        <thead>
            <tr>
                <th>Imagen</th>
                <th>
                    <a href="{{ route('admin.productos.index', ['q' => $q, 'dir' => $dir === 'asc' ? 'desc' : 'asc']) }}">
                        Nombre {{ $dir === 'asc' ? '▲' : '▼' }}
                    </a>
                </th>
                <th>SKU</th>
                <th>Categoría</th>
                <th>Proveedor</th>
                <th>Guardián</th>
                <th>Precio</th>
                <th>Stock</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($productos as $p)
                <tr>
                    <td>
                        @if ($p->imagen)
                            <img src="{{ asset($p->imagen) }}" alt="{{ $p->nombre }}"
                                 style="width:48px;height:48px;object-fit:cover;border-radius:8px;display:block">
                        @else
                            span class="adm-sinimg">Sin<br>foto</span>
                        @endif
                    </td>
                    <td><strong>{{ $p->nombre }}</strong></td>
                    <td>{{ $p->sku }}</td>
                    <td>{{ $p->categoria->nombre ?? '—' }}</td>
                    <td>{{ $p->proveedor->nombre ?? '—' }}</td>
                    <td>{{ $p->guardian ?? '—' }}</td>
                    <td>${{ number_format($p->precio, 2) }}</td>
                    <td>{{ $p->stock }}</td>
                    <td>
                        <span class="adm-badge {{ $p->activo ? 'si' : 'no' }}">{{ $p->activo ? 'Activo' : 'Inactivo' }}</span>
                    </td>
                    <td>
                        <div class="adm-acciones">
                            <a class="adm-btn chico sec" href="{{ route('admin.productos.edit', $p) }}">Editar</a>
                            @if (auth()->user()->esAdmin())
                            <form method="POST" action="{{ route('admin.productos.destroy', $p) }}"
                                  onsubmit="return confirm('¿Eliminar el producto {{ addslashes($p->nombre) }}?');">
                                @csrf
                                @method('DELETE')
                                <button class="adm-btn chico rojo" type="submit">Eliminar</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="10" class="adm-vacio">No hay productos{{ $q !== '' ? ' que coincidan con la búsqueda' : '' }}.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($productos->hasPages())
    <div class="adm-pag">
        @if ($productos->previousPageUrl())
            <a class="adm-btn chico sec" href="{{ $productos->previousPageUrl() }}">← Anterior</a>
        @endif
        <span>Página {{ $productos->currentPage() }} de {{ $productos->lastPage() }}</span>
        @if ($productos->nextPageUrl())
            <a class="adm-btn chico sec" href="{{ $productos->nextPageUrl() }}">Siguiente →</a>
        @endif
    </div>
@endif
@endsection
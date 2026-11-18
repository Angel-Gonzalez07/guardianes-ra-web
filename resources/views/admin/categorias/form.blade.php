@extends('layouts.admin')

@section('contenido')
@include('admin._estilos')

<div class="adm-form">
    <h1 class="adm-titulo" style="margin-top:0">{{ $categoria->exists ? 'Editar categoría' : 'Nueva categoría' }}</h1>

    <form method="POST"
          action="{{ $categoria->exists ? route('admin.categorias.update', $categoria) : route('admin.categorias.store') }}">
        @csrf
        @if ($categoria->exists)
            @method('PUT')
        @endif

        <label for="nombre">Nombre</label>
        <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $categoria->nombre) }}"
               required minlength="3" maxlength="80">

        <label for="descripcion">Descripción (opcional)</label>
        <textarea id="descripcion" name="descripcion" rows="3" maxlength="255">{{ old('descripcion', $categoria->descripcion) }}</textarea>

        <label class="check">
            <input type="hidden" name="activo" value="0">
            <input type="checkbox" name="activo" value="1" @checked(old('activo', $categoria->activo))>
            Categoría activa
        </label>

        <div class="botones">
            <button class="adm-btn" type="submit">Guardar</button>
            <a class="adm-btn sec" href="{{ route('admin.categorias.index') }}">Cancelar</a>
        </div>
    </form>
</div>
@endsection
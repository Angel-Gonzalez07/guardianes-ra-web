@extends('layouts.admin')

@section('contenido')
@include('admin._estilos')

<div class="adm-form">
    <h1 class="adm-titulo" style="margin-top:0">{{ $proveedor->exists ? 'Editar proveedor' : 'Nuevo proveedor' }}</h1>

    <form method="POST"
          action="{{ $proveedor->exists ? route('admin.proveedores.update', $proveedor) : route('admin.proveedores.store') }}">
        @csrf
        @if ($proveedor->exists)
            @method('PUT')
        @endif

        <label for="nombre">Nombre de la empresa</label>
        <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $proveedor->nombre) }}"
               required minlength="3" maxlength="120">

        <label for="contacto">Persona de contacto</label>
        <input type="text" id="contacto" name="contacto" value="{{ old('contacto', $proveedor->contacto) }}"
               required minlength="3" maxlength="100">

        <label for="email">Correo</label>
        <input type="email" id="email" name="email" value="{{ old('email', $proveedor->email) }}"
               required maxlength="150" placeholder="ventas@empresa.com">

        <label for="telefono">Teléfono (10 dígitos)</label>
        <input type="text" id="telefono" name="telefono" value="{{ old('telefono', $proveedor->telefono) }}"
               required inputmode="numeric" pattern="[0-9]{10}" minlength="10" maxlength="10"
               title="Exactamente 10 dígitos, sin espacios ni guiones">

        <label for="direccion">Dirección (opcional)</label>
        <input type="text" id="direccion" name="direccion" value="{{ old('direccion', $proveedor->direccion) }}"
               maxlength="200">

        <label class="check">
            <input type="hidden" name="activo" value="0">
            <input type="checkbox" name="activo" value="1" @checked(old('activo', $proveedor->activo))>
            Proveedor activo
        </label>

        <div class="botones">
            <button class="adm-btn" type="submit">Guardar</button>
            <a class="adm-btn sec" href="{{ route('admin.proveedores.index') }}">Cancelar</a>
        </div>
    </form>
</div>
@endsection
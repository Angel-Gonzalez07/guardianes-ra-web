@extends('layouts.admin')

@section('contenido')
@include('admin._estilos')

<div class="adm-form">
    <h1 class="adm-titulo" style="margin-top:0">{{ $cliente->exists ? 'Editar cliente' : 'Nuevo cliente' }}</h1>

    <form method="POST"
          action="{{ $cliente->exists ? route('admin.clientes.update', $cliente) : route('admin.clientes.store') }}">
        @csrf
        @if ($cliente->exists)
            @method('PUT')
        @endif

        <label for="user_id">Cuenta de usuario (opcional)</label>
        <select id="user_id" name="user_id">
            <option value="">— Sin cuenta —</option>
            @foreach ($cuentas as $u)
                <option value="{{ $u->id }}" @selected(old('user_id', $cliente->user_id) == $u->id)>
                    {{ $u->email }} ({{ $u->name }})
                </option>
            @endforeach
        </select>
        <small style="color:#7a6a60">Solo aparecen cuentas de cliente que todavía no están enlazadas.</small>

        <label for="nombre">Nombre(s)</label>
        <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $cliente->nombre) }}"
               required minlength="2" maxlength="100">

        <label for="apellidos">Apellidos</label>
        <input type="text" id="apellidos" name="apellidos" value="{{ old('apellidos', $cliente->apellidos) }}"
               required minlength="2" maxlength="100">

        <label for="telefono">Teléfono (10 dígitos)</label>
        <input type="text" id="telefono" name="telefono" value="{{ old('telefono', $cliente->telefono) }}"
               required inputmode="numeric" pattern="[0-9]{10}" minlength="10" maxlength="10"
               title="Exactamente 10 dígitos, sin espacios ni guiones">

        <label for="direccion">Dirección</label>
        <input type="text" id="direccion" name="direccion" value="{{ old('direccion', $cliente->direccion) }}"
               required minlength="5" maxlength="200" placeholder="Calle, número y colonia">

        <label for="ciudad">Ciudad</label>
        <input type="text" id="ciudad" name="ciudad" value="{{ old('ciudad', $cliente->ciudad) }}"
               required minlength="2" maxlength="80">

        <label for="estado">Estado</label>
        <select id="estado" name="estado" required>
            <option value="">— Elige un estado —</option>
            @foreach ($estados as $e)
                <option value="{{ $e }}" @selected(old('estado', $cliente->estado) === $e)>{{ $e }}</option>
            @endforeach
        </select>

        <label for="codigo_postal">Código postal (5 dígitos)</label>
        <input type="text" id="codigo_postal" name="codigo_postal" value="{{ old('codigo_postal', $cliente->codigo_postal) }}"
               required inputmode="numeric" pattern="[0-9]{5}" minlength="5" maxlength="5"
               title="Exactamente 5 dígitos">

        <label class="check">
            <input type="hidden" name="activo" value="0">
            <input type="checkbox" name="activo" value="1" @checked(old('activo', $cliente->activo))>
            Cliente activo
        </label>

        <div class="botones">
            <button class="adm-btn" type="submit">Guardar</button>
            <a class="adm-btn sec" href="{{ route('admin.clientes.index') }}">Cancelar</a>
        </div>
    </form>
</div>
@endsection
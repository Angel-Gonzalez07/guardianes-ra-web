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

        <label for="name">Nombre completo</label>
        <input type="text" id="name" name="name" value="{{ old('name', $cliente->name) }}"
               required minlength="3" maxlength="100">

        <label for="email">Correo institucional</label>
        <input type="email" id="email" name="email" value="{{ old('email', $cliente->email) }}"
               required maxlength="150" placeholder="usuario@utcv.edu.mx"
               pattern="[A-Za-z0-9._%+\-]+@[Uu][Tt][Cc][Vv]\.[Ee][Dd][Uu]\.[Mm][Xx]"
               title="Debe terminar en @utcv.edu.mx">

        <label for="password">Contraseña{{ $cliente->exists ? ' (déjala vacía para no cambiarla)' : '' }}</label>
        <input type="password" id="password" name="password"
               {{ $cliente->exists ? '' : 'required' }} minlength="8" maxlength="50" autocomplete="new-password">

        <label for="password_confirmation">Confirmar contraseña</label>
        <input type="password" id="password_confirmation" name="password_confirmation"
               {{ $cliente->exists ? '' : 'required' }} minlength="8" maxlength="50" autocomplete="new-password">

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
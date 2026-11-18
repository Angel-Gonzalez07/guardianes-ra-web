@extends('layouts.admin')

@section('contenido')
@include('admin._estilos')

@php $esYo = $usuario->exists && $usuario->id === auth()->id(); @endphp

<div class="adm-form">
    <h1 class="adm-titulo" style="margin-top:0">{{ $usuario->exists ? 'Editar usuario' : 'Nuevo usuario' }}</h1>

    <form method="POST"
          action="{{ $usuario->exists ? route('admin.usuarios.update', $usuario) : route('admin.usuarios.store') }}">
        @csrf
        @if ($usuario->exists)
            @method('PUT')
        @endif

        <label for="nombres">Nombre(s)</label>
        <input type="text" id="nombres" name="nombres" value="{{ old('nombres', $usuario->nombres) }}"
               required minlength="2" maxlength="60"
               pattern="[A-Za-zÁÉÍÓÚáéíóúÑñÜü]+([ '\-][A-Za-zÁÉÍÓÚáéíóúÑñÜü]+)*" title="Solo letras y un espacio entre palabras">

        <label for="apellidos">Apellidos</label>
        <input type="text" id="apellidos" name="apellidos" value="{{ old('apellidos', $usuario->apellidos) }}"
               required minlength="2" maxlength="60"
               pattern="[A-Za-zÁÉÍÓÚáéíóúÑñÜü]+([ '\-][A-Za-zÁÉÍÓÚáéíóúÑñÜü]+)*" title="Solo letras y un espacio entre palabras">

        <label for="email">Correo electrónico</label>
        <input type="email" id="email" name="email" value="{{ old('email', $usuario->email) }}"
               required maxlength="150" placeholder="correo@ejemplo.com">
        <small style="color:var(--muted);font-size:.78rem">Administrador y empleado deben usar un correo @utcv.edu.mx. Los clientes pueden usar cualquiera.</small>

        <label for="password">Contraseña{{ $usuario->exists ? ' (déjala vacía para no cambiarla)' : '' }}</label>
        <input type="password" id="password" name="password"
               {{ $usuario->exists ? '' : 'required' }} minlength="8" maxlength="50" autocomplete="new-password">

        <label for="password_confirmation">Confirmar contraseña</label>
        <input type="password" id="password_confirmation" name="password_confirmation"
               {{ $usuario->exists ? '' : 'required' }} minlength="8" maxlength="50" autocomplete="new-password">

        @if ($esYo)
            <input type="hidden" name="role_id" value="{{ $usuario->role_id }}">
            <input type="hidden" name="activo" value="1">
            <p style="margin-top:1rem;color:#8D5A00;background:#FFF3E0;padding:.6rem .8rem;border-radius:10px">
                Esta es tu propia cuenta: no puedes cambiar tu rol ni desactivarla.
            </p>
        @else
            <label for="role_id">Rol</label>
            <select id="role_id" name="role_id" required>
                <option value="">— Elige un rol —</option>
                @foreach ($roles as $r)
                    <option value="{{ $r->id }}" @selected(old('role_id', $usuario->role_id) == $r->id)>{{ $r->nombre }}</option>
                @endforeach
            </select>

            <label class="check">
                <input type="hidden" name="activo" value="0">
                <input type="checkbox" name="activo" value="1" @checked(old('activo', $usuario->activo))>
                Usuario activo
            </label>
        @endif

        <div class="botones">
            <button class="adm-btn" type="submit">Guardar</button>
            <a class="adm-btn sec" href="{{ route('admin.usuarios.index') }}">Cancelar</a>
        </div>
    </form>
</div>
@endsection

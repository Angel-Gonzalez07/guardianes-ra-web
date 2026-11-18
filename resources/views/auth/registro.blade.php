@extends('layouts.app')

@section('contenido')
<style>
    .reg-caja { max-width:440px; margin:2.5rem auto; background:#fff; border-radius:20px; padding:2rem 1.6rem; box-shadow:0 8px 24px rgba(93,64,55,.15); border-top:6px solid #2E7D32; }
    .reg-caja h1 { color:#2E7D32; text-align:center; margin-bottom:.3rem; }
    .reg-caja p.sub { text-align:center; margin-bottom:1.2rem; font-size:.95rem; }
    .reg-caja label { display:block; font-weight:bold; margin:.9rem 0 .3rem; color:#5D4037; }
    .reg-caja input { width:100%; padding:.65rem .8rem; border:2px solid #A5D6A7; border-radius:10px; font-size:1rem; background:#FFF8E1; }
    .reg-caja input:focus { outline:none; border-color:#2E7D32; }
    .reg-caja small { color:#5D4037; opacity:.8; }
    .reg-caja button { width:100%; margin-top:1.4rem; padding:.8rem; border:0; border-radius:999px; background:#FFB300; color:#5D4037; font-weight:bold; font-size:1rem; cursor:pointer; }
    .reg-caja button:hover { opacity:.9; }
    .reg-caja .pie { text-align:center; margin-top:1rem; font-size:.95rem; }
    .reg-caja .pie a { color:#2E7D32; font-weight:bold; }
</style>

<div class="reg-caja">
    <h1>Crear cuenta</h1>
    <p class="sub">Regístrate con tu correo institucional.</p>

    <form method="POST" action="{{ route('registro') }}">
        @csrf

        <label for="name">Nombre completo</label>
        <input type="text" id="name" name="name" value="{{ old('name') }}" required minlength="3" maxlength="100"
               pattern="[A-Za-zÁÉÍÓÚáéíóúÑñÜü .'\-]+" title="Solo letras, espacios, puntos, apóstrofes y guiones">

        <label for="email">Correo institucional</label>
        <input type="email" id="email" name="email" value="{{ old('email') }}" required maxlength="150"
               placeholder="usuario@utcv.edu.mx" pattern="[A-Za-z0-9._%+\-]+@[uU][tT][cC][vV]\.[eE][dD][uU]\.[mM][xX]"
               title="Debe ser un correo @utcv.edu.mx">
        <small>Solo correos @utcv.edu.mx</small>

        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password" required minlength="8" maxlength="50">
        <small>Entre 8 y 50 caracteres</small>

        <label for="password_confirmation">Confirmar contraseña</label>
        <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8" maxlength="50">

        <button type="submit">Registrarme</button>
    </form>

    <p class="pie">¿Ya tienes cuenta? <a href="{{ route('login') }}">Iniciar sesión</a></p>
</div>
@endsection
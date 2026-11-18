   @extends('layouts.app')

   @section('titulo', 'Iniciar sesión')

   @section('contenido')
       <div class="tarjeta" style="max-width: 420px; margin: 40px auto;">
           <h1>Guardianes RA</h1>
           <p>Inicia sesión para continuar.</p>
           <form method="POST" action="{{ route('login') }}">
               @csrf
               <label for="email">Correo</label>
               <input type="email" id="email" name="email" value="{{ old('email') }}" maxlength="100" placeholder="usuario@utcv.edu.mx">

               <label for="password">Contraseña</label>
               <input type="password" id="password" name="password" maxlength="50">

               <button class="btn" type="submit">Entrar</button>
           </form>
           <p style="text-align:center; margin-top:1rem;">
           ¿No tienes cuenta? <a href="{{ route('registro') }}" style="color:#2E7D32; font-weight:700;">Regístrate</a>
</p>
       </div>
   @endsection
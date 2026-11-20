<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Role;
use App\Models\User;
use Illuminate\Validation\Rules\Password;
use App\Rules\NombrePersona;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

   public function login(Request $request)
   {
       $datos = $request->validate([
           'email' => ['required','string','email','max:150'],
           'password' => ['required', 'string', 'min:8', 'max:50'],
       ], [
           'email.required' => 'El correo es obligatorio.',
           'email.max' => 'El correo no puede pasar de 100 caracteres.',
           'email.regex' => 'Escibe un Correo Valido',
           'password.required' => 'La contraseña es obligatoria.',
           'password.min' => 'La contraseña debe tener mínimo 8 caracteres.',
           'password.max' => 'La contraseña no puede pasar de 50 caracteres.',
       ]);

       $datos['email'] = strtolower($datos['email']);

       $entro = Auth::attempt([
           'email' => $datos['email'],
           'password' => $datos['password'],
           'activo' => true,
       ]);

       if (!$entro) {
           return back()
               ->withInput($request->only('email'))
               ->withErrors(['email' => 'Correo o contraseña incorrectos, o la cuenta está inactiva.']);
       }

       $request->session()->regenerate();

        /** @var \App\Models\User $usuario */
        $usuario = Auth::user();
        $destino = $usuario->esPersonal() ? 'admin.dashboard' : 'tienda';

       return redirect()->route($destino)
           ->with('exito', 'Bienvenido, ' . Auth::user()->name . '.');
   }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('inicio')->with('exito', 'Sesión cerrada correctamente.');
    }

        public function showRegistro()
    {
        return view('auth.registro');
    }

    public function registro(Request $request)
    {
        $request->merge([
            'nombres'   => NombrePersona::limpiar($request->input('nombres')),
            'apellidos' => NombrePersona::limpiar($request->input('apellidos')),
            'email'     => strtolower(trim((string) $request->input('email'))),
        ]);

        $datos = $request->validate([
            'nombres'   => ['required', 'string', 'min:2', 'max:60', new NombrePersona(3)],
            'apellidos' => ['required', 'string', 'min:2', 'max:60', new NombrePersona(3)],
            'email'     => ['required', 'string', 'email', 'max:150', 'unique:users,email'],
            'password'  => ['required', 'string', 'max:50', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()],
        ], [
            'nombres.required'   => 'El nombre o nombres son obligatorios.',
            'nombres.min'        => 'El nombre debe tener al menos 2 letras.',
            'nombres.max'        => 'El nombre no puede pasar de 60 caracteres.',
            'apellidos.required' => 'Los apellidos son obligatorios.',
            'apellidos.min'      => 'Los apellidos deben tener al menos 2 letras.',
            'apellidos.max'      => 'Los apellidos no pueden pasar de 60 caracteres.',
            'email.required'     => 'El correo es obligatorio.',
            'email.email'        => 'Escribe un correo válido.',
            'email.max'          => 'El correo no puede pasar de 150 caracteres.',
            'email.unique'       => 'Ese correo ya está registrado.',
            'password.required'  => 'La contraseña es obligatoria.',
            'password.min'       => 'La contraseña debe tener al menos 8 caracteres.',
            'password.letters'   => 'La contraseña debe incluir al menos una letra.',
            'password.mixed'     => 'La contraseña debe incluir mayúsculas y minúsculas.',
            'password.numbers'   => 'La contraseña debe incluir al menos un número.',
            'password.max'       => 'La contraseña no puede pasar de 50 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ], [
            'nombres'   => 'nombre(s)',
            'apellidos' => 'apellidos',
        ]);

        // El campo "name" se arma solo (nombres + apellidos) desde el modelo User
        $user = User::create([
            'nombres'   => $datos['nombres'],
            'apellidos' => $datos['apellidos'],
            'email'     => $datos['email'],
            'password'  => $datos['password'],
            'role_id'   => Role::where('nombre', 'cliente')->value('id'),
            'activo'    => true,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('inicio')
            ->with('exito', '¡Cuenta creada! Bienvenido(a), ' . $user->name . '.');
    }
}
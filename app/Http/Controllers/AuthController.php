<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Role;
use App\Models\User;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

   public function login(Request $request)
   {
       $datos = $request->validate([
           'email' => ['required', 'max:100', 'regex:/^[A-Za-z0-9._%+\-]+@utcv\.edu\.mx$/i'],
           'password' => ['required', 'string', 'min:8', 'max:50'],
       ], [
           'email.required' => 'El correo es obligatorio.',
           'email.max' => 'El correo no puede pasar de 100 caracteres.',
           'email.regex' => 'Solo se permiten correos institucionales @utcv.edu.mx.',
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
            'name'  => trim((string) $request->input('name')),
            'email' => strtolower(trim((string) $request->input('email'))),
        ]);

        $datos = $request->validate([
            'name'     => ['required', 'string', 'min:3', 'max:100', 'regex:/^[\pL\s.\'\-]+$/u'],
            'email'    => ['required', 'string', 'max:150', 'regex:/^[A-Za-z0-9._%+\-]+@utcv\.edu\.mx$/i', 'unique:users,email'],
            'password' => ['required', 'string', 'max:50', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()],
        ], [
            'name.required'      => 'El nombre es obligatorio.',
            'name.min'           => 'El nombre debe tener al menos 3 caracteres.',
            'name.max'           => 'El nombre no puede pasar de 100 caracteres.',
            'name.regex'         => 'El nombre solo puede llevar letras, espacios, puntos, apóstrofes y guiones.',
            'email.required'     => 'El correo es obligatorio.',
            'email.max'          => 'El correo no puede pasar de 150 caracteres.',
            'email.regex'        => 'Solo se permiten correos @utcv.edu.mx.',
            'email.unique'       => 'Ese correo ya está registrado.',
            'password.required'  => 'La contraseña es obligatoria.',
            'password.min'       => 'La contraseña debe tener al menos 8 caracteres.',
            'password.letters'   => 'La contraseña debe incluir al menos una letra.',
            'password.mixed'     => 'La contraseña debe incluir mayúsculas y minúsculas.',
            'password.numbers'   => 'La contraseña debe incluir al menos un número.',
            'password.max'       => 'La contraseña no puede pasar de 50 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        $user = User::create([
            'name'     => $datos['name'],
            'email'    => $datos['email'],
            'password' => $datos['password'],
            'role_id'  => Role::where('nombre', 'cliente')->value('id'),
            'activo'   => true,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('inicio')
            ->with('exito', '¡Cuenta creada! Bienvenido(a), ' . $user->name . '.');
    }
}
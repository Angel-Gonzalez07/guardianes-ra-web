<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Rules\NombrePersona;
use Illuminate\Validation\Rules\Password;

class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $dir = $request->query('dir') === 'desc' ? 'desc' : 'asc';

        $usuarios = User::with('role')
            ->when($q !== '', function ($query) use ($q) {
                $like = '%' . addcslashes($q, '%_\\') . '%';
                $query->where(function ($w) use ($like) {
                    $w->where('name', 'ILIKE', $like)
                      ->orWhere('email', 'ILIKE', $like)
                      ->orWhereHas('role', fn ($r) => $r->where('nombre', 'ILIKE', $like));
                });
            })
            ->orderBy('name', $dir)
            ->paginate(10)
            ->withQueryString();

        return view('admin.usuarios.index', compact('usuarios', 'q', 'dir'));
    }

    public function create()
    {
        return view('admin.usuarios.form', [
            'usuario' => new User(['activo' => true]),
            'roles'   => Role::orderBy('nombre')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $datos = $this->validar($request);
        User::create($datos);

        return redirect()->route('admin.usuarios.index')
            ->with('exito', 'Usuario creado correctamente.');
    }

    public function edit(User $usuario)
    {
        return view('admin.usuarios.form', [
            'usuario' => $usuario,
            'roles'   => Role::orderBy('nombre')->get(),
        ]);
    }

    public function update(Request $request, User $usuario)
    {
        $datos = $this->validar($request, $usuario);

        if ($usuario->id === Auth::id()) {
            if (! $datos['activo'] || (int) $datos['role_id'] !== (int) $usuario->role_id) {
                return back()->withInput()
                    ->withErrors(['No puedes desactivar tu propia cuenta ni cambiar tu propio rol.']);
            }
        }

        if (empty($datos['password'])) {
            unset($datos['password']); // vacía = conservar la contraseña actual
        }

        $usuario->update($datos);

        return redirect()->route('admin.usuarios.index')
            ->with('exito', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $usuario)
    {
        if ($usuario->id === Auth::id()) {
            return redirect()->route('admin.usuarios.index')
                ->withErrors(['No puedes eliminar tu propia cuenta.']);
        }

        

       if ($usuario->ventas()->exists()) {
            return redirect()->route('admin.usuarios.index')
                ->withErrors(['No se puede eliminar a «' . $usuario->name . '» porque tiene ventas registradas. Desactiva su cuenta en su lugar.']);
        }
        $usuario->delete();

        return redirect()->route('admin.usuarios.index')
            ->with('exito', 'Usuario eliminado correctamente.');
    }

        private function validar(Request $request, ?User $usuario = null): array
    {
        $request->merge([
            'nombres'   => NombrePersona::limpiar($request->input('nombres')),
            'apellidos' => NombrePersona::limpiar($request->input('apellidos')),
            'email'     => strtolower(trim((string) $request->input('email'))),
        ]);

        // El personal (admin y empleado) debe usar correo institucional; los clientes, cualquiera.
        $rol = Role::where('id', $request->input('role_id'))->value('nombre');
        $reglasCorreo = ['required', 'string', 'email', 'max:150', Rule::unique('users', 'email')->ignore($usuario?->id)];
        if (in_array($rol, ['admin', 'empleado'], true)) {
            $reglasCorreo[] = 'regex:/^[A-Za-z0-9._%+\-]+@utcv\.edu\.mx$/i';
        }

        $datos = $request->validate([
            'nombres'   => ['required', 'string', 'min:2', 'max:60', new NombrePersona(3)],
            'apellidos' => ['required', 'string', 'min:2', 'max:60', new NombrePersona(3)],
            'email'     => $reglasCorreo,
            'password'  => [$usuario ? 'nullable' : 'required', 'string', 'max:50', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()],
            'role_id'   => ['required', 'integer', 'exists:roles,id'],
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
            'email.regex'        => 'El personal (administrador y empleado) debe usar un correo @utcv.edu.mx.',
            'password.required'  => 'La contraseña es obligatoria.',
            'password.min'       => 'La contraseña debe tener al menos 8 caracteres.',
            'password.letters'   => 'La contraseña debe incluir al menos una letra.',
            'password.mixed'     => 'La contraseña debe incluir mayúsculas y minúsculas.',
            'password.numbers'   => 'La contraseña debe incluir al menos un número.',
            'password.max'       => 'La contraseña no puede pasar de 50 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'role_id.required'   => 'Elige un rol.',
            'role_id.exists'     => 'El rol elegido no existe.',
        ], [
            'nombres'   => 'nombre(s)',
            'apellidos' => 'apellidos',
        ]);

        $datos['activo'] = $request->boolean('activo');

        return $datos;
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

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

        $tieneCliente = DB::table('clientes')->where('user_id', $usuario->id)->exists();

        if ($tieneCliente) {
            return redirect()->route('admin.usuarios.index')
                ->withErrors(['No se puede eliminar a «' . $usuario->name . '» porque tiene un cliente enlazado. Elimina o cambia ese cliente primero.']);
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
            'name'  => trim((string) $request->input('name')),
            'email' => strtolower(trim((string) $request->input('email'))),
        ]);

        $datos = $request->validate([
            'name'     => ['required', 'string', 'min:3', 'max:100', 'regex:/^[\pL\s.\'\-]+$/u'],
            'email'    => ['required', 'string', 'max:150',
                           'regex:/^[A-Za-z0-9._%+\-]+@utcv\.edu\.mx$/i',
                           Rule::unique('users', 'email')->ignore($usuario?->id)],
            'password' => [$usuario ? 'nullable' : 'required', 'string', 'min:8', 'max:50', 'confirmed'],
            'role_id'  => ['required', 'integer', 'exists:roles,id'],
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
            'password.max'       => 'La contraseña no puede pasar de 50 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'role_id.required'   => 'Elige un rol.',
            'role_id.exists'     => 'El rol elegido no existe.',
        ]);

        $datos['activo'] = $request->boolean('activo');

        return $datos;
    }
}

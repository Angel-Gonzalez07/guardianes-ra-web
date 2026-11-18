<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Rules\NombrePersona;
use Illuminate\Validation\Rules\Password;

class ClienteController extends Controller
{
    private function rolId(): int
    {
        return (int) Role::where('nombre', 'cliente')->value('id');
    }

    // Solo se puede trabajar con usuarios que tengan rol cliente
    private function soloCliente(User $cliente): void
    {
        abort_unless((int) $cliente->role_id === $this->rolId(), 404);
    }

    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $dir = $request->query('dir') === 'desc' ? 'desc' : 'asc';

        $clientes = User::where('role_id', $this->rolId())
            ->withCount('ventas')
            ->when($q !== '', function ($query) use ($q) {
                $like = '%' . addcslashes($q, '%_\\') . '%';
                $query->where(function ($w) use ($like) {
                    $w->where('name', 'ILIKE', $like)
                      ->orWhere('email', 'ILIKE', $like);
                });
            })
            ->orderBy('name', $dir)
            ->paginate(10)
            ->withQueryString();

        return view('admin.clientes.index', compact('clientes', 'q', 'dir'));
    }

    public function create()
    {
        return view('admin.clientes.form', ['cliente' => new User(['activo' => true])]);
    }

    public function store(Request $request)
    {
        $datos = $this->validar($request);
        $datos['role_id'] = $this->rolId();
        User::create($datos);

        return redirect()->route('admin.clientes.index')
            ->with('exito', 'Cliente creado correctamente.');
    }

    public function edit(User $cliente)
    {
        $this->soloCliente($cliente);

        return view('admin.clientes.form', ['cliente' => $cliente]);
    }

    public function update(Request $request, User $cliente)
    {
        $this->soloCliente($cliente);

        $datos = $this->validar($request, $cliente);
        if (empty($datos['password'])) {
            unset($datos['password']); // vacía = conservar la contraseña actual
        }
        $cliente->update($datos);

        return redirect()->route('admin.clientes.index')
            ->with('exito', 'Cliente actualizado correctamente.');
    }

    public function destroy(User $cliente)
    {
        $this->soloCliente($cliente);

        if ($cliente->ventas()->exists()) {
            return redirect()->route('admin.clientes.index')
                ->withErrors(['No se puede eliminar a «' . $cliente->name . '» porque tiene ventas registradas. Desactiva su cuenta en su lugar.']);
        }

        $cliente->delete();

        return redirect()->route('admin.clientes.index')
            ->with('exito', 'Cliente eliminado correctamente.');
    }

    private function validar(Request $request, ?User $cliente = null): array
    {
        $request->merge([
            'name'  => trim((string) $request->input('name')),
            'email' => strtolower(trim((string) $request->input('email'))),
        ]);

        $datos = $request->validate([
            'name'     => ['required', 'string', 'min:3', 'max:100', 'regex:/^[\pL\s.\'\-]+$/u'],
            'email'    => ['required', 'string', 'max:150',
                           'regex:/^[A-Za-z0-9._%+\-]+@utcv\.edu\.mx$/i',
                           Rule::unique('users', 'email')->ignore($cliente?->id)],
            'password' => [$cliente ? 'nullable' : 'required', 'string', 'max:50', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()],
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

        $datos['activo'] = $request->boolean('activo');

        return $datos;
    }
}
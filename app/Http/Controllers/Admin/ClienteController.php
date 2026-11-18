<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClienteController extends Controller
{
    private const ESTADOS = [
        'Aguascalientes', 'Baja California', 'Baja California Sur', 'Campeche', 'Chiapas',
        'Chihuahua', 'Ciudad de México', 'Coahuila', 'Colima', 'Durango', 'Estado de México',
        'Guanajuato', 'Guerrero', 'Hidalgo', 'Jalisco', 'Michoacán', 'Morelos', 'Nayarit',
        'Nuevo León', 'Oaxaca', 'Puebla', 'Querétaro', 'Quintana Roo', 'San Luis Potosí',
        'Sinaloa', 'Sonora', 'Tabasco', 'Tamaulipas', 'Tlaxcala', 'Veracruz', 'Yucatán', 'Zacatecas',
    ];

    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $dir = $request->query('dir') === 'desc' ? 'desc' : 'asc';

        $clientes = Cliente::with('user')
            ->when($q !== '', function ($query) use ($q) {
                $like = '%' . addcslashes($q, '%_\\') . '%';
                $query->where(function ($w) use ($like) {
                    $w->where('nombre', 'ILIKE', $like)
                      ->orWhere('apellidos', 'ILIKE', $like)
                      ->orWhere('telefono', 'ILIKE', $like)
                      ->orWhere('ciudad', 'ILIKE', $like)
                      ->orWhere('estado', 'ILIKE', $like)
                      ->orWhere('codigo_postal', 'ILIKE', $like)
                      ->orWhereHas('user', fn ($u) => $u->where('email', 'ILIKE', $like));
                });
            })
            ->orderBy('apellidos', $dir)
            ->orderBy('nombre', $dir)
            ->paginate(10)
            ->withQueryString();

        return view('admin.clientes.index', compact('clientes', 'q', 'dir'));
    }

    public function create()
    {
        return view('admin.clientes.form', $this->datosFormulario(new Cliente(['activo' => true])));
    }

    public function store(Request $request)
    {
        $datos = $this->validar($request);
        Cliente::create($datos);

        return redirect()->route('admin.clientes.index')
            ->with('exito', 'Cliente creado correctamente.');
    }

    public function edit(Cliente $cliente)
    {
        return view('admin.clientes.form', $this->datosFormulario($cliente));
    }

    public function update(Request $request, Cliente $cliente)
    {
        $datos = $this->validar($request, $cliente);
        $cliente->update($datos);

        return redirect()->route('admin.clientes.index')
            ->with('exito', 'Cliente actualizado correctamente.');
    }

    public function destroy(Cliente $cliente)
    {
        // Ninguna otra tabla referencia a clientes por ahora.
        // Si más adelante se agregan pedidos, revisar aquí antes de borrar.
        // Borrar la ficha del cliente NO borra su cuenta de usuario.
        $cliente->delete();

        return redirect()->route('admin.clientes.index')
            ->with('exito', 'Cliente eliminado correctamente.');
    }

    private function datosFormulario(Cliente $cliente): array
    {
        $rolId = Role::where('nombre', 'cliente')->value('id');

        $ocupados = Cliente::whereNotNull('user_id')
            ->when($cliente->exists, fn ($q) => $q->where('id', '!=', $cliente->id))
            ->pluck('user_id');

        return [
            'cliente' => $cliente,
            'cuentas' => User::where('role_id', $rolId)
                            ->whereNotIn('id', $ocupados)
                            ->orderBy('email')->get(),
            'estados' => self::ESTADOS,
        ];
    }

    private function validar(Request $request, ?Cliente $cliente = null): array
    {
        $request->merge([
            'nombre'        => trim((string) $request->input('nombre')),
            'apellidos'     => trim((string) $request->input('apellidos')),
            'telefono'      => trim((string) $request->input('telefono')),
            'direccion'     => trim((string) $request->input('direccion')),
            'ciudad'        => trim((string) $request->input('ciudad')),
            'codigo_postal' => trim((string) $request->input('codigo_postal')),
        ]);

        $rolId = Role::where('nombre', 'cliente')->value('id');

        $datos = $request->validate([
            'user_id'       => ['nullable', 'integer',
                                Rule::exists('users', 'id')->where('role_id', $rolId),
                                Rule::unique('clientes', 'user_id')->ignore($cliente?->id)],
            'nombre'        => ['required', 'string', 'min:2', 'max:100', 'regex:/^[\pL\s.\'\-]+$/u'],
            'apellidos'     => ['required', 'string', 'min:2', 'max:100', 'regex:/^[\pL\s.\'\-]+$/u'],
            'telefono'      => ['required', 'regex:/^\d{10}$/'],
            'direccion'     => ['required', 'string', 'min:5', 'max:200', 'regex:/^[\pL\d\s.,#;:()\/°\-]+$/u'],
            'ciudad'        => ['required', 'string', 'min:2', 'max:80', 'regex:/^[\pL\s.\'\-]+$/u'],
            'estado'        => ['required', Rule::in(self::ESTADOS)],
            'codigo_postal' => ['required', 'regex:/^\d{5}$/'],
        ], [
            'user_id.exists'        => 'La cuenta elegida no existe o no es de un cliente.',
            'user_id.unique'        => 'Esa cuenta ya está enlazada a otro cliente.',
            'nombre.required'       => 'El nombre es obligatorio.',
            'nombre.min'            => 'El nombre debe tener al menos 2 caracteres.',
            'nombre.max'            => 'El nombre no puede pasar de 100 caracteres.',
            'nombre.regex'          => 'El nombre solo puede llevar letras, espacios, puntos, apóstrofes y guiones.',
            'apellidos.required'    => 'Los apellidos son obligatorios.',
            'apellidos.min'         => 'Los apellidos deben tener al menos 2 caracteres.',
            'apellidos.max'         => 'Los apellidos no pueden pasar de 100 caracteres.',
            'apellidos.regex'       => 'Los apellidos solo pueden llevar letras, espacios, puntos, apóstrofes y guiones.',
            'telefono.required'     => 'El teléfono es obligatorio.',
            'telefono.regex'        => 'El teléfono debe tener exactamente 10 dígitos, sin espacios ni guiones.',
            'direccion.required'    => 'La dirección es obligatoria.',
            'direccion.min'         => 'La dirección debe tener al menos 5 caracteres.',
            'direccion.max'         => 'La dirección no puede pasar de 200 caracteres.',
            'direccion.regex'       => 'La dirección tiene caracteres no permitidos.',
            'ciudad.required'       => 'La ciudad es obligatoria.',
            'ciudad.min'            => 'La ciudad debe tener al menos 2 caracteres.',
            'ciudad.max'            => 'La ciudad no puede pasar de 80 caracteres.',
            'ciudad.regex'          => 'La ciudad solo puede llevar letras, espacios, puntos, apóstrofes y guiones.',
            'estado.required'       => 'Elige un estado.',
            'estado.in'             => 'Elige un estado de la lista.',
            'codigo_postal.required' => 'El código postal es obligatorio.',
            'codigo_postal.regex'   => 'El código postal debe tener exactamente 5 dígitos.',
        ]);

        $datos['user_id'] = $datos['user_id'] ?? null;
        $datos['activo'] = $request->boolean('activo');

        return $datos;
    }
}
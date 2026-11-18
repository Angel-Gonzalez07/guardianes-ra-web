<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProveedorController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $dir = $request->query('dir') === 'desc' ? 'desc' : 'asc';

        $proveedores = Proveedor::query()
            ->when($q !== '', function ($query) use ($q) {
                $like = '%' . addcslashes($q, '%_\\') . '%';
                $query->where(function ($w) use ($like) {
                    $w->where('nombre', 'ILIKE', $like)
                      ->orWhere('contacto', 'ILIKE', $like)
                      ->orWhere('email', 'ILIKE', $like)
                      ->orWhere('telefono', 'ILIKE', $like);
                });
            })
            ->orderBy('nombre', $dir)
            ->paginate(10)
            ->withQueryString();

        return view('admin.proveedores.index', compact('proveedores', 'q', 'dir'));
    }

    public function create()
    {
        return view('admin.proveedores.form', [
            'proveedor' => new Proveedor(['activo' => true]),
        ]);
    }

    public function store(Request $request)
    {
        $datos = $this->validar($request);
        Proveedor::create($datos);

        return redirect()->route('admin.proveedores.index')
            ->with('exito', 'Proveedor creado correctamente.');
    }

    public function edit(Proveedor $proveedor)
    {
        return view('admin.proveedores.form', compact('proveedor'));
    }

    public function update(Request $request, Proveedor $proveedor)
    {
        $datos = $this->validar($request);
        $proveedor->update($datos);

        return redirect()->route('admin.proveedores.index')
            ->with('exito', 'Proveedor actualizado correctamente.');
    }

    public function destroy(Proveedor $proveedor)
    {
        $tieneProductos = DB::table('productos')
            ->where('proveedor_id', $proveedor->id)
            ->exists();

        if ($tieneProductos) {
            return redirect()->route('admin.proveedores.index')
                ->withErrors(['No se puede eliminar al proveedor «' . $proveedor->nombre . '» porque tiene productos asignados.']);
        }

        $proveedor->delete();

        return redirect()->route('admin.proveedores.index')
            ->with('exito', 'Proveedor eliminado correctamente.');
    }

    private function validar(Request $request): array
    {
        $request->merge([
            'nombre'    => trim((string) $request->input('nombre')),
            'contacto'  => trim((string) $request->input('contacto')),
            'email'     => strtolower(trim((string) $request->input('email'))),
            'telefono'  => trim((string) $request->input('telefono')),
            'direccion' => trim((string) $request->input('direccion')),
        ]);

        $datos = $request->validate([
            'nombre'    => ['required', 'string', 'min:3', 'max:120', 'regex:/^[\pL\d\s.,&\'\-]+$/u'],
            'contacto'  => ['required', 'string', 'min:3', 'max:100', 'regex:/^[\pL\s.\'\-]+$/u'],
            'email'     => ['required', 'string', 'max:150', 'email:rfc'],
            'telefono'  => ['required', 'regex:/^\d{10}$/'],
            'direccion' => ['nullable', 'string', 'max:200', 'regex:/^[\pL\d\s.,#;:()\/\-]+$/u'],
        ], [
            'nombre.required'    => 'El nombre del proveedor es obligatorio.',
            'nombre.min'         => 'El nombre debe tener al menos 3 caracteres.',
            'nombre.max'         => 'El nombre no puede pasar de 120 caracteres.',
            'nombre.regex'       => 'El nombre solo puede llevar letras, números, espacios y . , & \' -',
            'contacto.required'  => 'El nombre del contacto es obligatorio.',
            'contacto.min'       => 'El contacto debe tener al menos 3 caracteres.',
            'contacto.max'       => 'El contacto no puede pasar de 100 caracteres.',
            'contacto.regex'     => 'El contacto solo puede llevar letras, espacios, puntos, apóstrofes y guiones.',
            'email.required'     => 'El correo es obligatorio.',
            'email.max'          => 'El correo no puede pasar de 150 caracteres.',
            'email.email'        => 'Escribe un correo válido, por ejemplo ventas@empresa.com.',
            'telefono.required'  => 'El teléfono es obligatorio.',
            'telefono.regex'     => 'El teléfono debe tener exactamente 10 dígitos, sin espacios ni guiones.',
            'direccion.max'      => 'La dirección no puede pasar de 200 caracteres.',
            'direccion.regex'    => 'La dirección tiene caracteres no permitidos.',
        ]);

        $datos['direccion'] = $datos['direccion'] !== '' ? $datos['direccion'] : null;
        $datos['activo'] = $request->boolean('activo');

        return $datos;
    }
}
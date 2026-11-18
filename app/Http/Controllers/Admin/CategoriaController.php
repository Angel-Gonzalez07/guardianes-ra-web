<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CategoriaController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $dir = $request->query('dir') === 'desc' ? 'desc' : 'asc';

        $categorias = Categoria::query()
            ->when($q !== '', function ($query) use ($q) {
                $like = '%' . addcslashes($q, '%_\\') . '%';
                $query->where(function ($w) use ($like) {
                    $w->where('nombre', 'ILIKE', $like)
                      ->orWhere('descripcion', 'ILIKE', $like);
                });
            })
            ->orderBy('nombre', $dir)
            ->paginate(10)
            ->withQueryString();

        return view('admin.categorias.index', compact('categorias', 'q', 'dir'));
    }

    public function create()
    {
        return view('admin.categorias.form', [
            'categoria' => new Categoria(['activo' => true]),
        ]);
    }

    public function store(Request $request)
    {
        $datos = $this->validar($request);
        Categoria::create($datos);

        return redirect()->route('admin.categorias.index')
            ->with('exito', 'Categoría creada correctamente.');
    }

    public function edit(Categoria $categoria)
    {
        return view('admin.categorias.form', compact('categoria'));
    }

    public function update(Request $request, Categoria $categoria)
    {
        $datos = $this->validar($request, $categoria);
        $categoria->update($datos);

        return redirect()->route('admin.categorias.index')
            ->with('exito', 'Categoría actualizada correctamente.');
    }

    public function destroy(Categoria $categoria)
    {
        $tieneProductos = DB::table('productos')
            ->where('categoria_id', $categoria->id)
            ->exists();

        if ($tieneProductos) {
            return redirect()->route('admin.categorias.index')
                ->withErrors(['No se puede eliminar la categoría «' . $categoria->nombre . '» porque tiene productos asignados.']);
        }

        $categoria->delete();

        return redirect()->route('admin.categorias.index')
            ->with('exito', 'Categoría eliminada correctamente.');
    }

    private function validar(Request $request, ?Categoria $categoria = null): array
    {
        $request->merge([
            'nombre'      => trim((string) $request->input('nombre')),
            'descripcion' => trim((string) $request->input('descripcion')),
        ]);

        $datos = $request->validate([
            'nombre' => [
                'required', 'string', 'min:3', 'max:80',
                'regex:/^[\pL\d\s.,&\-]+$/u',
                Rule::unique('categorias', 'nombre')->ignore($categoria?->id),
            ],
            'descripcion' => [
                'nullable', 'string', 'max:255',
                'regex:/^[\pL\d\s.,;:()¡!¿?\-]+$/u',
            ],
        ], [
            'nombre.required'   => 'El nombre es obligatorio.',
            'nombre.min'        => 'El nombre debe tener al menos 3 caracteres.',
            'nombre.max'        => 'El nombre no puede pasar de 80 caracteres.',
            'nombre.regex'      => 'El nombre solo puede llevar letras, números, espacios y . , & -',
            'nombre.unique'     => 'Ya existe una categoría con ese nombre.',
            'descripcion.max'   => 'La descripción no puede pasar de 255 caracteres.',
            'descripcion.regex' => 'La descripción tiene caracteres no permitidos.',
        ]);

        $datos['descripcion'] = $datos['descripcion'] !== '' ? $datos['descripcion'] : null;
        $datos['activo'] = $request->boolean('activo');

        return $datos;
    }
}
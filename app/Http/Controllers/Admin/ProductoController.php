<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\Producto;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProductoController extends Controller
{
    private const GUARDIANES = ['Musguín', 'Goti', 'Lumi', 'Tronqui'];

    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $dir = $request->query('dir') === 'desc' ? 'desc' : 'asc';

        $productos = Producto::with(['categoria', 'proveedor'])
            ->when($q !== '', function ($query) use ($q) {
                $like = '%' . addcslashes($q, '%_\\') . '%';
                $query->where(function ($w) use ($like) {
                    $w->where('nombre', 'ILIKE', $like)
                      ->orWhere('sku', 'ILIKE', $like)
                      ->orWhere('guardian', 'ILIKE', $like)
                      ->orWhereHas('categoria', fn ($c) => $c->where('nombre', 'ILIKE', $like))
                      ->orWhereHas('proveedor', fn ($p) => $p->where('nombre', 'ILIKE', $like));
                });
            })
            ->orderBy('nombre', $dir)
            ->paginate(10)
            ->withQueryString();

        return view('admin.productos.index', compact('productos', 'q', 'dir'));
    }

    public function create()
    {
        return view('admin.productos.form', $this->datosFormulario(new Producto(['activo' => true, 'stock' => 0])));
    }

    public function store(Request $request)
    {
        $datos = $this->validar($request);
        $datos['imagen'] = $this->guardarImagen($request, $datos['sku']);

        Producto::create($datos);

        return redirect()->route('admin.productos.index')
            ->with('exito', 'Producto creado correctamente.');
    }

    public function edit(Producto $producto)
    {
        return view('admin.productos.form', $this->datosFormulario($producto));
    }

    public function update(Request $request, Producto $producto)
    {
        $datos = $this->validar($request, $producto);

        if ($request->hasFile('imagen')) {
            $this->borrarImagen($producto->imagen);
            $datos['imagen'] = $this->guardarImagen($request, $datos['sku']);
        }

        $producto->update($datos);

        return redirect()->route('admin.productos.index')
            ->with('exito', 'Producto actualizado correctamente.');
    }

    public function destroy(Producto $producto)
    {
        // Un producto vendido no se borra: el historial de ventas lo necesita.
        if (\App\Models\DetalleVenta::where('producto_id', $producto->id)->exists()) {
            return redirect()->route('admin.productos.index')
                ->withErrors(['No se puede eliminar «' . $producto->nombre . '» porque ya tiene ventas registradas. Desactívalo en su lugar.']);
        }
        
        $this->borrarImagen($producto->imagen);
        $producto->delete();

        return redirect()->route('admin.productos.index')
            ->with('exito', 'Producto eliminado correctamente.');
    }

    private function datosFormulario(Producto $producto): array
    {
        return [
            'producto'    => $producto,
            'categorias'  => Categoria::where('activo', true)
                                ->orWhere('id', $producto->categoria_id)
                                ->orderBy('nombre')->get(),
            'proveedores' => Proveedor::where('activo', true)
                                ->orWhere('id', $producto->proveedor_id)
                                ->orderBy('nombre')->get(),
            'guardianes'  => self::GUARDIANES,
        ];
    }

    private function guardarImagen(Request $request, string $sku): ?string
    {
        if (! $request->hasFile('imagen')) {
            return null;
        }

        $archivo = $request->file('imagen');
        $nombre = Str::slug($sku) . '-' . time() . '.' . $archivo->extension();
        $archivo->move(public_path('img/productos'), $nombre);

        return 'img/productos/' . $nombre;
    }

    private function borrarImagen(?string $ruta): void
    {
        if ($ruta && File::exists(public_path($ruta))) {
            File::delete(public_path($ruta));
        }
    }

    private function validar(Request $request, ?Producto $producto = null): array
    {
        $request->merge([
            'nombre'      => trim((string) $request->input('nombre')),
            'sku'         => strtoupper(trim((string) $request->input('sku'))),
            'descripcion' => trim((string) $request->input('descripcion')),
            'precio'      => str_replace(',', '.', trim((string) $request->input('precio'))),
        ]);

        $datos = $request->validate([
            'categoria_id' => ['required', 'integer', 'exists:categorias,id'],
            'proveedor_id' => ['required', 'integer', 'exists:proveedores,id'],
            'nombre'       => ['required', 'string', 'min:3', 'max:120', 'regex:/^[\pL\d\s.,&()\'\-]+$/u'],
            'sku'          => ['required', 'string', 'max:30', 'regex:/^[A-Z0-9\-]+$/',
                               Rule::unique('productos', 'sku')->ignore($producto?->id)],
            'descripcion'  => ['nullable', 'string', 'max:1000', 'regex:/^[\pL\d\s.,;:()¡!¿?\'"%$&\/\-]+$/u'],
            'guardian'     => ['nullable', Rule::in(self::GUARDIANES)],
            'precio'       => ['required', 'numeric', 'min:0.01', 'max:99999999.99', 'regex:/^\d{1,8}(\.\d{1,2})?$/'],
            'stock'        => ['required', 'integer', 'min:0', 'max:100000'],
            'imagen'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'categoria_id.required' => 'Elige una categoría.',
            'categoria_id.exists'   => 'La categoría elegida no existe.',
            'proveedor_id.required' => 'Elige un proveedor.',
            'proveedor_id.exists'   => 'El proveedor elegido no existe.',
            'nombre.required'       => 'El nombre es obligatorio.',
            'nombre.min'            => 'El nombre debe tener al menos 3 caracteres.',
            'nombre.max'            => 'El nombre no puede pasar de 120 caracteres.',
            'nombre.regex'          => 'El nombre solo puede llevar letras, números, espacios y . , & ( ) \' -',
            'sku.required'          => 'El SKU es obligatorio.',
            'sku.max'               => 'El SKU no puede pasar de 30 caracteres.',
            'sku.regex'             => 'El SKU solo puede llevar letras, números y guiones.',
            'sku.unique'            => 'Ya existe un producto con ese SKU.',
            'descripcion.max'       => 'La descripción no puede pasar de 1000 caracteres.',
            'descripcion.regex'     => 'La descripción tiene caracteres no permitidos.',
            'guardian.in'           => 'Elige un guardián de la lista.',
            'precio.required'       => 'El precio es obligatorio.',
            'precio.numeric'        => 'El precio debe ser un número.',
            'precio.min'            => 'El precio debe ser mayor a 0.',
            'precio.max'            => 'El precio es demasiado grande.',
            'precio.regex'          => 'El precio solo puede tener hasta 2 decimales.',
            'stock.required'        => 'El stock es obligatorio.',
            'stock.integer'         => 'El stock debe ser un número entero.',
            'stock.min'             => 'El stock no puede ser negativo.',
            'stock.max'             => 'El stock no puede pasar de 100000.',
            'imagen.image'          => 'El archivo debe ser una imagen.',
            'imagen.mimes'          => 'La imagen debe ser jpg, png o webp.',
            'imagen.max'            => 'La imagen no puede pesar más de 2 MB.',
            'imagen.uploaded'       => 'No se pudo subir la imagen (máximo 2 MB).',
        ]);

        $datos['descripcion'] = ($datos['descripcion'] ?? '') !== '' ? $datos['descripcion'] : null;
        $datos['guardian'] = $datos['guardian'] ?? null;
        $datos['activo'] = $request->boolean('activo');
        unset($datos['imagen']); // la imagen se maneja aparte

        return $datos;
    }
}

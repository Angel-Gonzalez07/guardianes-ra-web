<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Proveedor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProveedorController extends Controller
{
    // Columnas por las que se permite ordenar (lista blanca)
    private const ORDENES = ['nombre', 'ciudad', 'dias_entrega', 'activo'];

    public function index(Request $request)
    {
        [$query, $q, $estado, $orden, $dir] = $this->consulta($request);

        $proveedores = $query->withCount('productos')->paginate(10)->withQueryString();

        return view('admin.proveedores.index', compact('proveedores', 'q', 'estado', 'orden', 'dir'));
    }

    /** Ficha del proveedor: datos, productos que surte, valor en inventario y compras. */
    public function show(Proveedor $proveedor)
    {
        $productos = $proveedor->productos()->with('categoria')->orderBy('nombre')->get();
        $ordenes = $proveedor->ordenes()->latest()->take(5)->get();
        $ultimaCompra = $proveedor->ordenes()->where('estado', 'recibida')->latest('recibida_at')->first();

        $resumen = [
            'productos'  => $productos->count(),
            'unidades'   => (int) $productos->sum('stock'),
            'valor'      => (float) $productos->sum(fn ($p) => (float) $p->precio * $p->stock),
            'pendientes' => $proveedor->ordenes()->whereIn('estado', ['borrador', 'enviada'])->count(),
        ];

        return view('admin.proveedores.show', compact('proveedor', 'productos', 'ordenes', 'ultimaCompra', 'resumen'));
    }

    /** Descarga el listado (con los mismos filtros de la pantalla) en CSV. */
    public function exportar(Request $request): StreamedResponse
    {
        [$query] = $this->consulta($request);
        $archivo = 'proveedores-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $salida = fopen('php://output', 'w');
            fwrite($salida, "\xEF\xBB\xBF"); // BOM: Excel lee bien los acentos

            $columnas = ['Nombre', 'RFC', 'Contacto', 'Correo', 'Teléfono', 'Ciudad', 'Estado', 'Días de entrega', 'Condiciones de pago', 'Estatus'];
            fputcsv($salida, $columnas, ',', '"', '');

            $query->chunk(200, function ($lote) use ($salida) {
                foreach ($lote as $p) {
                    $fila = [
                        $p->nombre, $p->rfc, $p->contacto, $p->email, $p->telefono, $p->ciudad,
                        $p->estado_republica, $p->dias_entrega, $p->condiciones_pago,
                        $p->activo ? 'Activo' : 'Inactivo',
                    ];
                    fputcsv($salida, array_map(fn ($v) => self::celdaSegura($v), $fila), ',', '"', '');
                }
            });

            fclose($salida);
        }, $archivo, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function create()
    {
        return view('admin.proveedores.form', [
            'proveedor' => new Proveedor(['activo' => true]),
            'estados'   => config('tienda.estados'),
            'pagos'     => config('tienda.condiciones_pago'),
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
        return view('admin.proveedores.form', [
            'proveedor' => $proveedor,
            'estados'   => config('tienda.estados'),
            'pagos'     => config('tienda.condiciones_pago'),
        ]);
    }

    public function update(Request $request, Proveedor $proveedor)
    {
        $datos = $this->validar($request, $proveedor);
        $proveedor->update($datos);

        return redirect()->route('admin.proveedores.index')
            ->with('exito', 'Proveedor actualizado correctamente.');
    }

    public function destroy(Proveedor $proveedor)
    {
        if ($proveedor->productos()->exists()) {
            return redirect()->route('admin.proveedores.index')
                ->withErrors(['No se puede eliminar al proveedor «' . $proveedor->nombre . '» porque tiene productos asignados.']);
        }

        if ($proveedor->ordenes()->exists()) {
            return redirect()->route('admin.proveedores.index')
                ->withErrors(['No se puede eliminar al proveedor «' . $proveedor->nombre . '» porque tiene órdenes de compra. Desactívalo en su lugar.']);
        }

        $proveedor->delete();

        return redirect()->route('admin.proveedores.index')
            ->with('exito', 'Proveedor eliminado correctamente.');
    }

    /** Búsqueda + filtro + orden, compartidos por el listado y la exportación. */
    private function consulta(Request $request): array
    {
        $q = trim((string) $request->query('q', ''));
        $estado = in_array($request->query('estado'), ['activos', 'inactivos'], true) ? $request->query('estado') : '';
        $orden = in_array($request->query('orden'), self::ORDENES, true) ? $request->query('orden') : 'nombre';
        $dir = $request->query('dir') === 'desc' ? 'desc' : 'asc';

        $query = Proveedor::query()
            ->when($q !== '', function (Builder $query) use ($q) {
                $like = '%' . addcslashes($q, '%_\\') . '%';
                $query->where(function ($w) use ($like) {
                    $w->where('nombre', 'ILIKE', $like)
                      ->orWhere('contacto', 'ILIKE', $like)
                      ->orWhere('email', 'ILIKE', $like)
                      ->orWhere('telefono', 'ILIKE', $like)
                      ->orWhere('rfc', 'ILIKE', $like)
                      ->orWhere('ciudad', 'ILIKE', $like);
                });
            })
            ->when($estado === 'activos', fn (Builder $query) => $query->where('activo', true))
            ->when($estado === 'inactivos', fn (Builder $query) => $query->where('activo', false))
            ->orderBy($orden, $dir)
            ->orderBy('nombre')
            ->orderBy('id');

        return [$query, $q, $estado, $orden, $dir];
    }

    /** Evita que Excel ejecute fórmulas (=, +, -, @) escritas por alguien en un campo. */
    private static function celdaSegura(mixed $valor): string
    {
        $texto = (string) $valor;

        return preg_match('/^[=+\-@\t\r]/', $texto) ? "'" . $texto : $texto;
    }

    private function validar(Request $request, ?Proveedor $proveedor = null): array
    {
        $request->merge([
            'nombre'    => trim((string) $request->input('nombre')),
            'contacto'  => trim((string) $request->input('contacto')),
            'email'     => strtolower(trim((string) $request->input('email'))),
            'telefono'  => trim((string) $request->input('telefono')),
            'direccion' => trim((string) $request->input('direccion')),
            'rfc'       => mb_strtoupper(preg_replace('/\s+/', '', (string) $request->input('rfc'))),
            'ciudad'    => trim((string) $request->input('ciudad')),
            'notas'     => trim((string) $request->input('notas')),
        ]);

        $datos = $request->validate([
            'nombre'           => ['required', 'string', 'min:3', 'max:120', 'regex:/^[\pL\d\s.,&\'\-]+$/u'],
            'contacto'         => ['required', 'string', 'min:3', 'max:100', 'regex:/^[\pL\s.\'\-]+$/u'],
            'email'            => ['required', 'string', 'max:150', 'email:rfc'],
            'telefono'         => ['required', 'regex:/^\d{10}$/'],
            'direccion'        => ['nullable', 'string', 'max:200', 'regex:/^[\pL\d\s.,#;:()\/\-]+$/u'],
            'rfc'              => ['required', 'string', 'regex:/^[A-ZÑ&]{3,4}\d{6}[A-Z\d]{3}$/u',
                                   Rule::unique('proveedores', 'rfc')->ignore($proveedor?->id),
                                   function ($atributo, $valor, $fallar) {
                                       // La fecha del RFC (AAMMDD) debe existir; se prueba con un año bisiesto
                                       if (preg_match('/^[A-ZÑ&]{3,4}\d{2}(\d{2})(\d{2})/u', $valor, $m)
                                           && ! checkdate((int) $m[1], (int) $m[2], 2000)) {
                                           $fallar('El RFC tiene una fecha no válida (los 6 dígitos son AAMMDD).');
                                       }
                                   }],
            'ciudad'           => ['required', 'string', 'min:2', 'max:80', 'regex:/^[\pL\s.\'\-]+$/u'],
            'estado_republica' => ['required', Rule::in(config('tienda.estados'))],
            'dias_entrega'     => ['required', 'integer', 'between:1,90'],
            'condiciones_pago' => ['required', Rule::in(config('tienda.condiciones_pago'))],
            'notas'            => ['nullable', 'string', 'max:500'],
        ], [
            'nombre.required'           => 'El nombre del proveedor es obligatorio.',
            'nombre.min'                => 'El nombre debe tener al menos 3 caracteres.',
            'nombre.max'                => 'El nombre no puede pasar de 120 caracteres.',
            'nombre.regex'              => 'El nombre solo puede llevar letras, números, espacios y . , & \' -',
            'contacto.required'         => 'El nombre del contacto es obligatorio.',
            'contacto.min'              => 'El contacto debe tener al menos 3 caracteres.',
            'contacto.max'              => 'El contacto no puede pasar de 100 caracteres.',
            'contacto.regex'            => 'El contacto solo puede llevar letras, espacios, puntos, apóstrofes y guiones.',
            'email.required'            => 'El correo es obligatorio.',
            'email.max'                 => 'El correo no puede pasar de 150 caracteres.',
            'email.email'               => 'Escribe un correo válido, por ejemplo ventas@empresa.com.',
            'telefono.required'         => 'El teléfono es obligatorio.',
            'telefono.regex'            => 'El teléfono debe tener exactamente 10 dígitos, sin espacios ni guiones.',
            'direccion.max'             => 'La dirección no puede pasar de 200 caracteres.',
            'direccion.regex'           => 'La dirección tiene caracteres no permitidos.',
            'rfc.required'              => 'El RFC es obligatorio.',
            'rfc.regex'                 => 'El RFC no tiene un formato válido: 3 o 4 letras, 6 dígitos de fecha y 3 caracteres de homoclave (12 o 13 en total).',
            'rfc.unique'                => 'Ese RFC ya está registrado con otro proveedor.',
            'ciudad.required'           => 'La ciudad es obligatoria.',
            'ciudad.min'                => 'La ciudad debe tener al menos 2 caracteres.',
            'ciudad.max'                => 'La ciudad no puede pasar de 80 caracteres.',
            'ciudad.regex'              => 'La ciudad solo puede llevar letras, espacios, puntos, apóstrofes y guiones.',
            'estado_republica.required' => 'Elige el estado.',
            'estado_republica.in'       => 'El estado elegido no es válido.',
            'dias_entrega.required'     => 'Indica los días de entrega.',
            'dias_entrega.integer'      => 'Los días de entrega deben ser un número entero.',
            'dias_entrega.between'      => 'Los días de entrega deben estar entre 1 y 90.',
            'condiciones_pago.required' => 'Elige las condiciones de pago.',
            'condiciones_pago.in'       => 'Las condiciones de pago elegidas no son válidas.',
            'notas.max'                 => 'Las notas no pueden pasar de 500 caracteres.',
        ]);

        $datos['direccion'] = $datos['direccion'] ?: null;
        $datos['notas'] = $datos['notas'] ?: null;
        $datos['activo'] = $request->boolean('activo');

        return $datos;
    }
}

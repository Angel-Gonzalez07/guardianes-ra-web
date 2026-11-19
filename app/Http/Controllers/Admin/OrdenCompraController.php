<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrdenCompra;
use App\Models\Proveedor;
use App\Support\Inventario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class OrdenCompraController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $estado = in_array($request->query('estado'), OrdenCompra::ESTADOS, true) ? $request->query('estado') : '';
        $proveedorId = (int) $request->query('proveedor', 0);
        $dir = $request->query('dir') === 'asc' ? 'asc' : 'desc';

        $ordenes = OrdenCompra::with('proveedor')
            ->withCount('detalles')
            ->when($estado !== '', fn ($query) => $query->where('estado', $estado))
            ->when($proveedorId > 0, fn ($query) => $query->where('proveedor_id', $proveedorId))
            ->when($q !== '', fn ($query) => $query->where('folio', 'ILIKE', '%' . addcslashes($q, '%_\\') . '%'))
            ->orderBy('created_at', $dir)
            ->paginate(10)
            ->withQueryString();

        return view('admin.compras.index', [
            'ordenes'     => $ordenes,
            'q'           => $q,
            'estado'      => $estado,
            'proveedorId' => $proveedorId,
            'dir'         => $dir,
            'estados'     => OrdenCompra::ESTADOS,
            'proveedores' => Proveedor::orderBy('nombre')->get(['id', 'nombre']),
        ]);
    }

    /** Paso 1: elegir proveedor. Paso 2 (con ?proveedor=ID): elegir productos y cantidades. */
    public function create(Request $request)
    {
        $proveedores = Proveedor::where('activo', true)->orderBy('nombre')->get();
        $proveedor = $proveedores->firstWhere('id', (int) $request->query('proveedor'));

        return view('admin.compras.form', [
            'orden'       => new OrdenCompra(),
            'proveedores' => $proveedores,
            'proveedor'   => $proveedor,
            'productos'   => $proveedor ? $proveedor->productos()->where('activo', true)->orderBy('nombre')->get() : collect(),
            'cantidades'  => [],
            'costos'      => [],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'proveedor_id' => ['required', 'integer', Rule::exists('proveedores', 'id')->where('activo', true)],
        ], [
            'proveedor_id.required' => 'Elige un proveedor.',
            'proveedor_id.exists'   => 'El proveedor elegido no existe o está inactivo.',
        ]);

        $proveedor = Proveedor::findOrFail($request->input('proveedor_id'));
        [$lineas, $notas] = $this->validarLineas($request, $proveedor);

        $orden = DB::transaction(function () use ($proveedor, $lineas, $notas) {
            $orden = OrdenCompra::create([
                'folio'        => 'OC-' . now()->format('ymd') . '-' . strtoupper(Str::random(5)),
                'proveedor_id' => $proveedor->id,
                'user_id'      => Auth::id(),
                'estado'       => 'borrador',
                'total'        => 0,
                'notas'        => $notas,
            ]);
            $this->guardarLineas($orden, $lineas);

            return $orden;
        });

        return redirect()->route('admin.compras.show', $orden)
            ->with('exito', 'Orden ' . $orden->folio . ' guardada como borrador.');
    }

    public function show(OrdenCompra $compra)
    {
        $compra->load(['proveedor', 'user', 'detalles']);

        return view('admin.compras.show', [
            'orden'      => $compra,
            'siguientes' => OrdenCompra::TRANSICIONES[$compra->estado] ?? [],
        ]);
    }

    public function edit(OrdenCompra $compra)
    {
        if ($compra->estado !== 'borrador') {
            return redirect()->route('admin.compras.show', $compra)
                ->withErrors(['Solo se pueden editar las órdenes en borrador.']);
        }

        $compra->load('detalles');
        $proveedor = $compra->proveedor;

        return view('admin.compras.form', [
            'orden'       => $compra,
            'proveedores' => collect([$proveedor]),
            'proveedor'   => $proveedor,
            'productos'   => $proveedor->productos()->where('activo', true)->orderBy('nombre')->get(),
            'cantidades'  => $compra->detalles->pluck('cantidad', 'producto_id')->all(),
            'costos'      => $compra->detalles->pluck('costo_unitario', 'producto_id')->all(),
        ]);
    }

    public function update(Request $request, OrdenCompra $compra)
    {
        if ($compra->estado !== 'borrador') {
            return redirect()->route('admin.compras.show', $compra)
                ->withErrors(['Solo se pueden editar las órdenes en borrador.']);
        }

        [$lineas, $notas] = $this->validarLineas($request, $compra->proveedor);

        DB::transaction(function () use ($compra, $lineas, $notas) {
            $compra->detalles()->delete();
            $compra->update(['notas' => $notas]);
            $this->guardarLineas($compra, $lineas);
        });

        return redirect()->route('admin.compras.show', $compra)
            ->with('exito', 'Orden ' . $compra->folio . ' actualizada.');
    }

    /** Cambia el estado. Al marcar "recibida" el stock sube solo y se registra el movimiento. */
    public function estado(Request $request, OrdenCompra $compra)
    {
        $datos = $request->validate([
            'estado' => ['required', Rule::in(OrdenCompra::ESTADOS)],
        ], [
            'estado.required' => 'Elige un estado.',
            'estado.in'       => 'El estado elegido no es válido.',
        ]);

        $cambio = DB::transaction(function () use ($compra, $datos) {
            // Se bloquea la orden para que no se pueda recibir dos veces a la vez
            $orden = OrdenCompra::with('detalles')->whereKey($compra->id)->lockForUpdate()->first();

            if (! in_array($datos['estado'], OrdenCompra::TRANSICIONES[$orden->estado] ?? [], true)) {
                return false;
            }

            $campos = ['estado' => $datos['estado']];

            if ($datos['estado'] === 'enviada') {
                $campos['enviada_at'] = now();
            }
            if ($datos['estado'] === 'recibida') {
                foreach ($orden->detalles as $d) {
                    Inventario::mover($d->producto_id, $d->cantidad, 'compra', $orden->folio);
                }
                $campos['recibida_at'] = now();
            }
            if ($datos['estado'] === 'cancelada') {
                $campos['cancelada_at'] = now();
            }

            $orden->update($campos);

            return true;
        });

        if (! $cambio) {
            return back()->withErrors(['No se puede pasar la orden de «' . $compra->estado . '» a «' . $datos['estado'] . '».']);
        }

        $mensaje = $datos['estado'] === 'recibida'
            ? 'Orden ' . $compra->folio . ' recibida: el stock de los productos ya se actualizó.'
            : 'Orden ' . $compra->folio . ' actualizada a «' . $datos['estado'] . '».';

        return back()->with('exito', $mensaje);
    }

    /**
     * Lee cantidad[producto_id] y costo[producto_id]; solo cuentan las líneas con cantidad mayor a 0.
     * Devuelve [líneas, notas].
     */
    private function validarLineas(Request $request, Proveedor $proveedor): array
    {
        $request->merge(['notas' => trim((string) $request->input('notas'))]);

        $request->validate([
            'notas'      => ['nullable', 'string', 'max:200', 'regex:/^[\pL\d\s.,;:#()°\/\-]+$/u'],
            'cantidad'   => ['nullable', 'array'],
            'cantidad.*' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'costo'      => ['nullable', 'array'],
            'costo.*'    => ['nullable', 'numeric', 'min:0', 'max:99999.99'],
        ], [
            'notas.max'          => 'Las notas no pueden pasar de 200 caracteres.',
            'notas.regex'        => 'Las notas tienen caracteres no permitidos.',
            'cantidad.*.integer' => 'Las cantidades deben ser números enteros.',
            'cantidad.*.min'     => 'Las cantidades no pueden ser negativas.',
            'cantidad.*.max'     => 'La cantidad máxima por producto es 9,999.',
            'costo.*.numeric'    => 'Los costos deben ser números.',
            'costo.*.min'        => 'Los costos no pueden ser negativos.',
            'costo.*.max'        => 'El costo máximo por pieza es $99,999.99.',
        ]);

        // Solo se aceptan productos activos de ESTE proveedor
        $productos = $proveedor->productos()->where('activo', true)->get()->keyBy('id');
        $lineas = [];
        $errores = [];

        foreach ((array) $request->input('cantidad', []) as $productoId => $cantidad) {
            $cantidad = (int) $cantidad;
            if ($cantidad <= 0) {
                continue;
            }

            $producto = $productos->get((int) $productoId);
            if (! $producto) {
                $errores[] = 'Uno de los productos no pertenece a este proveedor.';
                continue;
            }

            $costo = (float) $request->input("costo.$productoId", 0);
            if ($costo <= 0) {
                $errores[] = 'Indica el costo unitario de «' . $producto->nombre . '».';
                continue;
            }

            $lineas[] = ['producto' => $producto, 'cantidad' => $cantidad, 'costo' => round($costo, 2)];
        }

        if (! $lineas && ! $errores) {
            $errores[] = 'Agrega al menos un producto con cantidad mayor a 0.';
        }
        if ($errores) {
            throw ValidationException::withMessages(['lineas' => array_values(array_unique($errores))]);
        }

        return [$lineas, $request->input('notas') ?: null];
    }

    private function guardarLineas(OrdenCompra $orden, array $lineas): void
    {
        $total = 0.0;

        foreach ($lineas as $l) {
            $subtotal = round($l['cantidad'] * $l['costo'], 2);
            $total += $subtotal;

            $orden->detalles()->create([
                'producto_id'    => $l['producto']->id,
                'nombre'         => $l['producto']->nombre,
                'cantidad'       => $l['cantidad'],
                'costo_unitario' => $l['costo'],
                'subtotal'       => $subtotal,
            ]);
        }

        $orden->update(['total' => round($total, 2)]);
    }
}

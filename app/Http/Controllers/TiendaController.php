<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TiendaController extends Controller
{
    private const MAX_POR_PRODUCTO = 10;

    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $categoriaId = (int) $request->query('categoria', 0);

        $productos = Producto::with('categoria')
            ->where('activo', true)
            ->whereHas('categoria', fn ($c) => $c->where('activo', true))
            ->when($categoriaId > 0, fn ($query) => $query->where('categoria_id', $categoriaId))
            ->when($q !== '', function ($query) use ($q) {
                $like = '%' . addcslashes($q, '%_\\') . '%';
                $query->where(function ($w) use ($like) {
                    $w->where('nombre', 'ILIKE', $like)
                      ->orWhere('guardian', 'ILIKE', $like)
                      ->orWhere('descripcion', 'ILIKE', $like);
                });
            })
            ->orderBy('nombre')
            ->paginate(12)
            ->withQueryString();

        return view('tienda.index', [
            'productos'   => $productos,
            'categorias'  => Categoria::where('activo', true)->orderBy('nombre')->get(),
            'categoriaId' => $categoriaId,
            'q'           => $q,
        ]);
    }

    public function carrito()
    {
        $items = $this->itemsCarrito();
        $cliente = Cliente::where('user_id', auth()->id())->first();

        return view('tienda.carrito', [
            'items'     => $items,
            'total'     => $items->sum('subtotal'),
            'direccion' => ($cliente && $cliente->direccion) ? trim($cliente->direccion . ', ' . $cliente->ciudad, ', ') : '',
            'metodos'   => Venta::METODOS,
        ]);
    }

    public function agregar(Request $request, Producto $producto)
    {
        $datos = $request->validate([
            'cantidad' => ['required', 'integer', 'min:1', 'max:' . self::MAX_POR_PRODUCTO],
        ], [
            'cantidad.required' => 'Indica la cantidad.',
            'cantidad.integer'  => 'La cantidad debe ser un número entero.',
            'cantidad.min'      => 'La cantidad mínima es 1.',
            'cantidad.max'      => 'Máximo ' . self::MAX_POR_PRODUCTO . ' piezas por producto.',
        ]);

        if (! $producto->activo || $producto->stock < 1) {
            return back()->withErrors(['«' . $producto->nombre . '» no está disponible por ahora.']);
        }

        $carrito = session('carrito', []);
        $nueva = ($carrito[$producto->id] ?? 0) + $datos['cantidad'];

        if ($nueva > self::MAX_POR_PRODUCTO) {
            return back()->withErrors(['Máximo ' . self::MAX_POR_PRODUCTO . ' piezas por producto.']);
        }
        if ($nueva > $producto->stock) {
            return back()->withErrors(['Solo hay ' . $producto->stock . ' piezas de «' . $producto->nombre . '».']);
        }

        $carrito[$producto->id] = $nueva;
        session(['carrito' => $carrito]);

        return back()->with('exito', '«' . $producto->nombre . '» se agregó al carrito.');
    }

    public function actualizar(Request $request, Producto $producto)
    {
        $datos = $request->validate([
            'cantidad' => ['required', 'integer', 'min:1', 'max:' . self::MAX_POR_PRODUCTO],
        ], [
            'cantidad.required' => 'Indica la cantidad.',
            'cantidad.integer'  => 'La cantidad debe ser un número entero.',
            'cantidad.min'      => 'La cantidad mínima es 1.',
            'cantidad.max'      => 'Máximo ' . self::MAX_POR_PRODUCTO . ' piezas por producto.',
        ]);

        if ($datos['cantidad'] > $producto->stock) {
            return back()->withErrors(['Solo hay ' . $producto->stock . ' piezas de «' . $producto->nombre . '».']);
        }

        $carrito = session('carrito', []);
        if (isset($carrito[$producto->id])) {
            $carrito[$producto->id] = $datos['cantidad'];
            session(['carrito' => $carrito]);
        }

        return back()->with('exito', 'Carrito actualizado.');
    }

    public function quitar(Producto $producto)
    {
        $carrito = session('carrito', []);
        unset($carrito[$producto->id]);
        session(['carrito' => $carrito]);

        return back()->with('exito', '«' . $producto->nombre . '» se quitó del carrito.');
    }

    public function confirmar(Request $request)
    {
        $request->merge(['direccion_envio' => trim((string) $request->input('direccion_envio'))]);

        $datos = $request->validate([
            'metodo_pago'     => ['required', Rule::in(Venta::METODOS)],
            'direccion_envio' => ['required', 'string', 'min:10', 'max:200', 'regex:/^[\pL\d\s.,#;:()\/\-]+$/u'],
        ], [
            'metodo_pago.required'     => 'Elige un método de pago.',
            'metodo_pago.in'           => 'El método de pago no es válido.',
            'direccion_envio.required' => 'La dirección de envío es obligatoria.',
            'direccion_envio.min'      => 'La dirección debe tener al menos 10 caracteres.',
            'direccion_envio.max'      => 'La dirección no puede pasar de 200 caracteres.',
            'direccion_envio.regex'    => 'La dirección tiene caracteres no permitidos.',
        ]);

        $carrito = session('carrito', []);
        if (empty($carrito)) {
            return redirect()->route('tienda')->withErrors(['Tu carrito está vacío.']);
        }

        try {
            $venta = DB::transaction(function () use ($carrito, $datos) {
                // lockForUpdate evita que dos compras gasten la misma pieza a la vez
                $productos = Producto::whereIn('id', array_keys($carrito))->lockForUpdate()->get()->keyBy('id');

                $total = 0.0;
                $lineas = [];

                foreach ($carrito as $id => $cantidad) {
                    $p = $productos->get($id);

                    if (! $p || ! $p->activo) {
                        throw new \DomainException('Un producto de tu carrito ya no está disponible.');
                    }
                    if ($p->stock < $cantidad) {
                        throw new \DomainException('Solo quedan ' . $p->stock . ' piezas de «' . $p->nombre . '».');
                    }

                    $subtotal = round((float) $p->precio * $cantidad, 2);
                    $total += $subtotal;
                    $lineas[] = [
                        'producto_id'     => $p->id,
                        'nombre'          => $p->nombre,
                        'precio_unitario' => $p->precio,
                        'cantidad'        => (int) $cantidad,
                        'subtotal'        => $subtotal,
                    ];
                }

                $venta = Venta::create([
                    'folio'           => 'GRA-' . now()->format('ymd') . '-' . strtoupper(Str::random(5)),
                    'user_id'         => auth()->id(),
                    'total'           => round($total, 2),
                    'estado'          => 'pendiente',
                    'metodo_pago'     => $datos['metodo_pago'],
                    'direccion_envio' => $datos['direccion_envio'],
                ]);

                foreach ($lineas as $linea) {
                    $venta->detalles()->create($linea);
                    Producto::where('id', $linea['producto_id'])->decrement('stock', $linea['cantidad']);
                }

                return $venta;
            });
        } catch (\DomainException $e) {
            return redirect()->route('tienda.carrito')->withInput()->withErrors([$e->getMessage()]);
        }

        session()->forget('carrito');

        return redirect()->route('tienda.pedido', $venta)
            ->with('exito', '¡Pedido ' . $venta->folio . ' registrado! Te avisaremos cuando se confirme el pago.');
    }

    public function pedidos()
    {
        $pedidos = Venta::where('user_id', auth()->id())
            ->withCount('detalles')
            ->latest()
            ->paginate(10);

        return view('tienda.pedidos', compact('pedidos'));
    }

    public function pedido(Venta $venta)
    {
        // Un cliente solo puede ver SUS pedidos
        abort_unless($venta->user_id === auth()->id(), 404);

        $venta->load('detalles');

        return view('tienda.pedido', compact('venta'));
    }

    /** Líneas del carrito con los datos actuales de la base de datos. */
    private function itemsCarrito(): Collection
    {
        $carrito = session('carrito', []);
        if (empty($carrito)) {
            return collect();
        }

        $productos = Producto::whereIn('id', array_keys($carrito))->where('activo', true)->get()->keyBy('id');

        return collect($carrito)
            ->map(function ($cantidad, $id) use ($productos) {
                $p = $productos->get($id);
                if (! $p || $p->stock < 1) {
                    return null;
                }
                $cantidad = min((int) $cantidad, $p->stock);

                return [
                    'producto' => $p,
                    'cantidad' => $cantidad,
                    'subtotal' => round((float) $p->precio * $cantidad, 2),
                ];
            })
            ->filter()
            ->values();
    }
}

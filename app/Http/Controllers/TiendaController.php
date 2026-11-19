<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Producto;
use App\Models\Venta;
use App\Rules\NombrePersona;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TiendaController extends Controller
{
    private const MAX_POR_PRODUCTO = 10;

    /* ===================== Catálogo ===================== */

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

    /* ===================== Carrito ===================== */

    public function carrito()
    {
        $items = $this->itemsCarrito();

        return view('tienda.carrito', ['items' => $items] + $this->totales($items));
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

    /* ===================== Proceso de compra ===================== */

    // Paso 2: datos de envío
    public function envio()
    {
        $items = $this->itemsCarrito();
        if ($items->isEmpty()) {
            return redirect()->route('tienda.carrito')->withErrors(['Tu carrito está vacío.']);
        }

        $dato = session('checkout.envio');
        if (! $dato) {
            // Reutiliza los datos del último pedido, si existe
            $ultima = Venta::where('user_id', auth()->id())->whereNotNull('calle_numero')->latest()->first();
            $dato = $ultima
                ? $ultima->only(['destinatario', 'telefono', 'calle_numero', 'colonia', 'codigo_postal', 'ciudad', 'estado_republica', 'referencias'])
                : ['destinatario' => auth()->user()->name];
        }

        return view('tienda.envio', [
            'items'   => $items,
            'dato'    => $dato,
            'estados' => config('tienda.estados'),
        ] + $this->totales($items));
    }

    public function guardarEnvio(Request $request)
    {
        $request->merge([
            'destinatario'  => NombrePersona::limpiar($request->input('destinatario')),
            'telefono'      => trim((string) $request->input('telefono')),
            'calle_numero'  => trim((string) $request->input('calle_numero')),
            'colonia'       => trim((string) $request->input('colonia')),
            'codigo_postal' => trim((string) $request->input('codigo_postal')),
            'ciudad'        => trim((string) $request->input('ciudad')),
            'referencias'   => trim((string) $request->input('referencias')),
        ]);

        $datos = $request->validate([
            'destinatario'     => ['required', 'string', 'min:3', 'max:100', new NombrePersona(6)],
            'telefono'         => ['required', 'regex:/^\d{10}$/'],
            'calle_numero'     => ['required', 'string', 'min:5', 'max:120', 'regex:/^[\pL\d\s.,#°\/\-]+$/u'],
            'colonia'          => ['required', 'string', 'min:3', 'max:80', 'regex:/^[\pL\d\s.,#°\/\-]+$/u'],
            'codigo_postal'    => ['required', 'regex:/^\d{5}$/'],
            'ciudad'           => ['required', 'string', 'min:2', 'max:80', 'regex:/^[\pL\s.\'\-]+$/u'],
            'estado_republica' => ['required', Rule::in(config('tienda.estados'))],
            'referencias'      => ['nullable', 'string', 'max:200', 'regex:/^[\pL\d\s.,#:;()\/°\-]+$/u'],
        ], [
            'destinatario.required'      => 'Escribe el nombre de quien recibe.',
            'destinatario.min'           => 'El nombre de quien recibe debe tener al menos 3 caracteres.',
            'destinatario.max'           => 'El nombre de quien recibe no puede pasar de 100 caracteres.',
            'telefono.required'          => 'El teléfono es obligatorio.',
            'telefono.regex'             => 'El teléfono debe tener exactamente 10 dígitos, sin espacios ni guiones.',
            'calle_numero.required'      => 'La calle y el número son obligatorios.',
            'calle_numero.min'           => 'La calle debe tener al menos 5 caracteres.',
            'calle_numero.max'           => 'La calle y el número no pueden pasar de 120 caracteres.',
            'calle_numero.regex'         => 'La calle tiene caracteres no permitidos.',
            'colonia.required'           => 'La colonia es obligatoria.',
            'colonia.min'                => 'La colonia debe tener al menos 3 caracteres.',
            'colonia.max'                => 'La colonia no puede pasar de 80 caracteres.',
            'colonia.regex'              => 'La colonia tiene caracteres no permitidos.',
            'codigo_postal.required'     => 'El código postal es obligatorio.',
            'codigo_postal.regex'        => 'El código postal debe tener exactamente 5 dígitos.',
            'ciudad.required'            => 'La ciudad es obligatoria.',
            'ciudad.min'                 => 'La ciudad debe tener al menos 2 caracteres.',
            'ciudad.max'                 => 'La ciudad no puede pasar de 80 caracteres.',
            'ciudad.regex'               => 'La ciudad solo puede llevar letras, espacios, puntos, apóstrofes y guiones.',
            'estado_republica.required'  => 'Elige un estado.',
            'estado_republica.in'        => 'Elige un estado de la lista.',
            'referencias.max'            => 'Las referencias no pueden pasar de 200 caracteres.',
            'referencias.regex'          => 'Las referencias tienen caracteres no permitidos.',
        ], [
            'destinatario' => 'nombre de quien recibe',
        ]);

        session(['checkout.envio' => $datos]);

        return redirect()->route('tienda.pago');
    }

    // Paso 3: método de pago
    public function pago()
    {
        $items = $this->itemsCarrito();
        if ($items->isEmpty()) {
            return redirect()->route('tienda.carrito')->withErrors(['Tu carrito está vacío.']);
        }
        if (! session('checkout.envio')) {
            return redirect()->route('tienda.envio');
        }

        return view('tienda.pago', [
            'items'   => $items,
            'metodos' => Venta::METODOS,
            'metodo'  => session('checkout.metodo_pago'),
        ] + $this->totales($items));
    }

    public function guardarPago(Request $request)
    {
        $datos = $request->validate([
            'metodo_pago' => ['required', Rule::in(Venta::METODOS)],
        ], [
            'metodo_pago.required' => 'Elige un método de pago.',
            'metodo_pago.in'       => 'El método de pago no es válido.',
        ]);

        session(['checkout.metodo_pago' => $datos['metodo_pago']]);

        return redirect()->route('tienda.revision');
    }

    // Paso 4: revisión final
    public function revision()
    {
        $items = $this->itemsCarrito();
        if ($items->isEmpty()) {
            return redirect()->route('tienda.carrito')->withErrors(['Tu carrito está vacío.']);
        }
        if (! session('checkout.envio')) {
            return redirect()->route('tienda.envio');
        }
        if (! session('checkout.metodo_pago')) {
            return redirect()->route('tienda.pago');
        }

        return view('tienda.revision', [
            'items'  => $items,
            'envioD' => session('checkout.envio'),
            'metodo' => session('checkout.metodo_pago'),
        ] + $this->totales($items));
    }

    public function confirmar(Request $request)
    {
        $request->validate([
            'acepto' => ['accepted'],
        ], [
            'acepto.accepted' => 'Debes aceptar los términos y el aviso de privacidad para realizar el pedido.',
        ]);

        $carrito = session('carrito', []);
        if (empty($carrito)) {
            return redirect()->route('tienda')->withErrors(['Tu carrito está vacío.']);
        }

        $envio = session('checkout.envio');
        $metodo = session('checkout.metodo_pago');
        if (! $envio) {
            return redirect()->route('tienda.envio');
        }
        if (! in_array($metodo, Venta::METODOS, true)) {
            return redirect()->route('tienda.pago');
        }

        try {
            $venta = DB::transaction(function () use ($carrito, $envio, $metodo) {
                // lockForUpdate evita que dos compras gasten la misma pieza a la vez
                $productos = Producto::whereIn('id', array_keys($carrito))->lockForUpdate()->get()->keyBy('id');

                $subtotal = 0.0;
                $lineas = [];

                foreach ($carrito as $id => $cantidad) {
                    $p = $productos->get($id);

                    if (! $p || ! $p->activo) {
                        throw new \DomainException('Un producto de tu carrito ya no está disponible.');
                    }
                    if ($p->stock < $cantidad) {
                        throw new \DomainException('Solo quedan ' . $p->stock . ' piezas de «' . $p->nombre . '».');
                    }

                    $linea = round((float) $p->precio * $cantidad, 2);
                    $subtotal += $linea;
                    $lineas[] = [
                        'producto_id'     => $p->id,
                        'nombre'          => $p->nombre,
                        'precio_unitario' => $p->precio,
                        'cantidad'        => (int) $cantidad,
                        'subtotal'        => $linea,
                    ];
                }

                $subtotal = round($subtotal, 2);
                $costoEnvio = Venta::costoEnvioPara($subtotal);

                $direccion = mb_substr(
                    $envio['calle_numero'] . ', Col. ' . $envio['colonia'] . ', C.P. ' . $envio['codigo_postal']
                        . ', ' . $envio['ciudad'] . ', ' . $envio['estado_republica'],
                    0,
                    200
                );

                $venta = Venta::create([
                    'folio'              => 'GRA-' . now()->format('ymd') . '-' . strtoupper(Str::random(5)),
                    'user_id'            => auth()->id(),
                    'subtotal'           => $subtotal,
                    'costo_envio'        => $costoEnvio,
                    'total'              => round($subtotal + $costoEnvio, 2),
                    'estado'             => 'pendiente',
                    'metodo_pago'        => $metodo,
                    'direccion_envio'    => $direccion,
                    'destinatario'       => $envio['destinatario'],
                    'telefono'           => $envio['telefono'],
                    'calle_numero'       => $envio['calle_numero'],
                    'colonia'            => $envio['colonia'],
                    'codigo_postal'      => $envio['codigo_postal'],
                    'ciudad'             => $envio['ciudad'],
                    'estado_republica'   => $envio['estado_republica'],
                    'referencias'        => $envio['referencias'] ?: null,
                    'acepto_terminos_at' => now(),
                ]);

                foreach ($lineas as $linea) {
                    $venta->detalles()->create($linea);
                    Producto::where('id', $linea['producto_id'])->decrement('stock', $linea['cantidad']);
                }

                return $venta;
            });
        } catch (\DomainException $e) {
            return redirect()->route('tienda.carrito')->withErrors([$e->getMessage()]);
        }

        session()->forget(['carrito', 'checkout']);

        return redirect()->route('tienda.pedido', $venta)
            ->with('exito', 'Pedido ' . $venta->folio . ' registrado. Guarda tu folio para darle seguimiento.');
    }

    /* ===================== Mis pedidos ===================== */

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

    // El cliente puede cancelar mientras el pedido siga pendiente
    public function cancelar(Venta $venta)
    {
        abort_unless($venta->user_id === auth()->id(), 404);

        try {
            DB::transaction(function () use ($venta) {
                $actual = Venta::whereKey($venta->id)->lockForUpdate()->first();

                if ($actual->estado !== 'pendiente') {
                    throw new \DomainException('Este pedido ya no se puede cancelar porque está en estado «' . $actual->estado . '». Contacta a la tienda.');
                }

                foreach ($actual->detalles as $d) {
                    Producto::where('id', $d->producto_id)->increment('stock', $d->cantidad);
                }
                $actual->update(['estado' => 'cancelada', 'cancelada_at' => now()]);
            });
        } catch (\DomainException $e) {
            return back()->withErrors([$e->getMessage()]);
        }

        return back()->with('exito', 'Pedido ' . $venta->folio . ' cancelado.');
    }

    /* ===================== Apoyos ===================== */

    private function totales(Collection $items): array
    {
        $subtotal = round((float) $items->sum('subtotal'), 2);
        $envio = $subtotal > 0 ? Venta::costoEnvioPara($subtotal) : 0.0;

        return [
            'subtotal' => $subtotal,
            'envio'    => $envio,
            'total'    => round($subtotal + $envio, 2),
        ];
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
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Support\Inventario;

class VentaController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $estado = in_array($request->query('estado'), Venta::ESTADOS, true) ? $request->query('estado') : '';
        $dir = $request->query('dir') === 'asc' ? 'asc' : 'desc';

        $ventas = Venta::with('user')
            ->withCount('detalles')
            ->when($estado !== '', fn ($query) => $query->where('estado', $estado))
            ->when($q !== '', function ($query) use ($q) {
                $like = '%' . addcslashes($q, '%_\\') . '%';
                $query->where(function ($w) use ($like) {
                    $w->where('folio', 'ILIKE', $like)
                      ->orWhereHas('user', fn ($u) => $u->where('name', 'ILIKE', $like)->orWhere('email', 'ILIKE', $like));
                });
            })
            ->orderBy('created_at', $dir)
            ->paginate(10)
            ->withQueryString();

        return view('admin.ventas.index', [
            'ventas'  => $ventas,
            'q'       => $q,
            'estado'  => $estado,
            'dir'     => $dir,
            'estados' => Venta::ESTADOS,
        ]);
    }

    public function show(Venta $venta)
    {
        $venta->load(['user', 'detalles']);

        return view('admin.ventas.show', [
            'venta'      => $venta,
            'siguientes' => Venta::TRANSICIONES[$venta->estado] ?? [],
        ]);
    }

        public function estado(Request $request, Venta $venta)
    {
        $datos = $request->validate([
            'estado'      => ['required', Rule::in(Venta::ESTADOS)],
            'paqueteria'  => ['nullable', 'required_if:estado,enviada', Rule::in(config('tienda.paqueterias'))],
            'numero_guia' => ['nullable', 'required_if:estado,enviada', 'string', 'max:40', 'regex:/^[A-Za-z0-9\-]+$/'],
        ], [
            'estado.required'          => 'Elige un estado.',
            'estado.in'                => 'El estado elegido no es válido.',
            'paqueteria.required_if'   => 'Indica la paquetería para marcar el pedido como enviado.',
            'paqueteria.in'            => 'Elige una paquetería de la lista.',
            'numero_guia.required_if'  => 'Indica el número de guía para marcar el pedido como enviado.',
            'numero_guia.regex'        => 'La guía solo puede llevar letras, números y guiones.',
        ]);

        $permitidos = Venta::TRANSICIONES[$venta->estado] ?? [];

        if (! in_array($datos['estado'], $permitidos, true)) {
            return back()->withErrors(['No se puede pasar el pedido de «' . $venta->estado . '» a «' . $datos['estado'] . '».']);
        }

        DB::transaction(function () use ($venta, $datos) {
            if ($datos['estado'] === 'cancelada') {
                foreach ($venta->detalles as $d) {
                Inventario::mover($d->producto_id, $d->cantidad, 'cancelacion', $venta->folio);
                }
            }

            $campos = ['estado' => $datos['estado']];
            $fechas = ['pagada' => 'pagada_at', 'enviada' => 'enviada_at', 'entregada' => 'entregada_at', 'cancelada' => 'cancelada_at'];
            $campos[$fechas[$datos['estado']]] = now();

            if ($datos['estado'] === 'enviada') {
                $campos['paqueteria'] = $datos['paqueteria'];
                $campos['numero_guia'] = $datos['numero_guia'];
            }

            $venta->update($campos);
        });

        return back()->with('exito', 'Pedido ' . $venta->folio . ' actualizado a «' . $datos['estado'] . '».');
    }
}


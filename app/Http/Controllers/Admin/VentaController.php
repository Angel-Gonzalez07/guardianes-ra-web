<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

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
            'estado' => ['required', Rule::in(Venta::ESTADOS)],
        ], [
            'estado.required' => 'Elige un estado.',
            'estado.in'       => 'El estado elegido no es válido.',
        ]);

        $permitidos = Venta::TRANSICIONES[$venta->estado] ?? [];

        if (! in_array($datos['estado'], $permitidos, true)) {
            return back()->withErrors(['No se puede pasar el pedido de «' . $venta->estado . '» a «' . $datos['estado'] . '».']);
        }

        DB::transaction(function () use ($venta, $datos) {
            if ($datos['estado'] === 'cancelada') {
                foreach ($venta->detalles as $d) {
                    Producto::where('id', $d->producto_id)->increment('stock', $d->cantidad);
                }
            }
            $venta->update(['estado' => $datos['estado']]);
        });

        return back()->with('exito', 'Pedido ' . $venta->folio . ' actualizado a «' . $datos['estado'] . '».');
    }
}

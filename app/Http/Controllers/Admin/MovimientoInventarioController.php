<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MovimientoInventario;
use Illuminate\Http\Request;

class MovimientoInventarioController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $tipo = in_array($request->query('tipo'), ['entrada', 'salida'], true) ? $request->query('tipo') : '';
        $motivo = array_key_exists((string) $request->query('motivo'), MovimientoInventario::MOTIVOS) ? $request->query('motivo') : '';
        $dir = $request->query('dir') === 'asc' ? 'asc' : 'desc';

        $movimientos = MovimientoInventario::with(['producto', 'user'])
            ->when($tipo !== '', fn ($query) => $query->where('tipo', $tipo))
            ->when($motivo !== '', fn ($query) => $query->where('motivo', $motivo))
            ->when($q !== '', function ($query) use ($q) {
                $like = '%' . addcslashes($q, '%_\\') . '%';
                $query->where(function ($w) use ($like) {
                    $w->where('referencia', 'ILIKE', $like)
                      ->orWhereHas('producto', fn ($p) => $p->where('nombre', 'ILIKE', $like)->orWhere('sku', 'ILIKE', $like));
                });
            })
            ->orderBy('created_at', $dir)
            ->orderBy('id', $dir)
            ->paginate(15)
            ->withQueryString();

        return view('admin.inventario.index', [
            'movimientos' => $movimientos,
            'q'           => $q,
            'tipo'        => $tipo,
            'motivo'      => $motivo,
            'dir'         => $dir,
            'motivos'     => MovimientoInventario::MOTIVOS,
        ]);
    }
}

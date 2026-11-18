<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Models\Venta;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $esAdmin = auth()->user()->esAdmin();
        $totales = [
            'categorias'  => ['total' => Categoria::count(),  'activos' => Categoria::where('activo', true)->count()],
            'productos'   => ['total' => Producto::count(),   'activos' => Producto::where('activo', true)->count()],
            'proveedores' => ['total' => Proveedor::count(),  'activos' => Proveedor::where('activo', true)->count()],
            'clientes'    => ['total' => User::whereHas('role', fn ($r) => $r->where('nombre', 'cliente'))->count(), 'activos' => User::where('activo', true)->whereHas('role', fn ($r) => $r->where('nombre', 'cliente'))->count()],
            'usuarios'    => ['total' => User::count(),       'activos' => User::where('activo', true)->count()],
        ];

        $inventario = [
            'unidades'   => (int) Producto::sum('stock'),
            'valor'      => (float) Producto::selectRaw('COALESCE(SUM(precio * stock), 0) AS valor')->value('valor'),
            'sin_stock'  => Producto::where('stock', 0)->count(),
            'stock_bajo' => Producto::where('stock', '>', 0)->where('stock', '<=', 5)->count(),
        ];

        $recientes = Producto::with('categoria')->latest()->take(5)->get();

        $porCategoria = DB::table('categorias')
            ->leftJoin('productos', 'productos.categoria_id', '=', 'categorias.id')
            ->select('categorias.nombre', DB::raw('COUNT(productos.id) AS total'))
            ->groupBy('categorias.id', 'categorias.nombre')
            ->orderByDesc('total')
            ->orderBy('categorias.nombre')
            ->limit(6)
            ->get();

        $porGuardian = DB::table('productos')
            ->whereNotNull('guardian')
            ->select('guardian', DB::raw('COUNT(*) AS total'))
            ->groupBy('guardian')
            ->orderByDesc('total')
            ->get();
                /* ---------- Ventas ---------- */
        $inicioMes = now()->startOfMonth();

        $ventas = [
            'pedidos'      => Venta::count(),
            'pendientes'   => Venta::where('estado', 'pendiente')->count(),
            'canceladas'   => Venta::where('estado', 'cancelada')->count(),
            'mes_pedidos'  => Venta::confirmadas()->where('created_at', '>=', $inicioMes)->count(),
            'ingresos'     => (float) Venta::confirmadas()->sum('total'),
            'ingresos_mes' => (float) Venta::confirmadas()->where('created_at', '>=', $inicioMes)->sum('total'),
            'ticket'       => (float) (Venta::confirmadas()->avg('total') ?? 0),
        ];

        $masVendidos = DB::table('venta_detalles')
            ->join('ventas', 'ventas.id', '=', 'venta_detalles.venta_id')
            ->whereIn('ventas.estado', Venta::CONFIRMADAS)
            ->select('venta_detalles.nombre', DB::raw('SUM(venta_detalles.cantidad) AS unidades'), DB::raw('SUM(venta_detalles.subtotal) AS importe'))
            ->groupBy('venta_detalles.producto_id', 'venta_detalles.nombre')
            ->orderByDesc('unidades')
            ->limit(5)
            ->get();

        $ultimasVentas = Venta::with('user')->latest()->take(5)->get();

        // Solo administrador
        $registrados = null;
        $topClientes = collect();
        $ingresosMes = collect();

        if ($esAdmin) {
            $registrados = [
                'clientes' => User::whereHas('role', fn ($r) => $r->where('nombre', 'cliente'))->count(),
                'nuevos'   => User::whereHas('role', fn ($r) => $r->where('nombre', 'cliente'))->where('created_at', '>=', $inicioMes)->count(),
                'personal' => User::whereHas('role', fn ($r) => $r->whereIn('nombre', ['admin', 'empleado']))->count(),
            ];

            $topClientes = DB::table('ventas')
                ->join('users', 'users.id', '=', 'ventas.user_id')
                ->whereIn('ventas.estado', Venta::CONFIRMADAS)
                ->select('users.name', DB::raw('COUNT(ventas.id) AS pedidos'), DB::raw('SUM(ventas.total) AS gastado'))
                ->groupBy('users.id', 'users.name')
                ->orderByDesc('gastado')
                ->limit(5)
                ->get();

            $meses = collect(range(5, 0))->map(fn ($i) => now()->startOfMonth()->subMonths($i));
            $filas = Venta::confirmadas()
                ->where('created_at', '>=', $meses->first())
                ->get(['total', 'created_at'])
                ->groupBy(fn ($v) => $v->created_at->format('Y-m'));

            $ingresosMes = $meses->map(fn ($m) => [
                'mes'    => $m->translatedFormat('M Y'),
                'total'  => (float) ($filas->get($m->format('Y-m'))?->sum('total') ?? 0),
                'ventas' => $filas->get($m->format('Y-m'))?->count() ?? 0,
            ]);
        }

        return view('admin.dashboard', compact(
            'esAdmin', 'totales', 'inventario', 'recientes', 'porCategoria', 'porGuardian',
            'ventas', 'masVendidos', 'ultimasVentas', 'registrados', 'topClientes', 'ingresosMes'
        ));
    }
}
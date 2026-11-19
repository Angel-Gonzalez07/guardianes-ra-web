<?php

use App\Http\Controllers\Admin\CategoriaController;
use App\Http\Controllers\Admin\ClienteController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductoController;
use App\Http\Controllers\Admin\ProveedorController; // <- esta línea faltaba
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\VentaController;
use App\Http\Controllers\TiendaController;
use App\Models\Categoria;
use App\Models\Producto;
use App\Http\Controllers\Admin\MovimientoInventarioController;
use App\Http\Controllers\Admin\OrdenCompraController;

Route::get('/', function () {
    // Categorías con su número de productos disponibles ("próximamente" si no hay)
    $categoriasTienda = Categoria::where('activo', true)
        ->withCount(['productos' => fn ($q) => $q->where('activo', true)->where('stock', '>', 0)])
        ->get()
        ->keyBy('nombre');

    // Hasta 4 productos recientes para la sección "Compra mis productos"
    $destacados = Producto::where('activo', true)
        ->where('stock', '>', 0)
        ->whereHas('categoria', fn ($c) => $c->where('activo', true))
        ->latest()
        ->take(4)
        ->get();

    return view('landing', compact('categoriasTienda', 'destacados'));
})->name('inicio');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware(['guest', 'throttle:login']);
Route::get('/registro', [AuthController::class, 'showRegistro'])->name('registro')->middleware('guest');
Route::post('/registro', [AuthController::class, 'registro'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Admin y empleado: ver, crear y editar (sin eliminar)
Route::middleware(['auth', 'rol:admin,empleado'])->prefix('admin')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('admin.dashboard');

    Route::resource('categorias', CategoriaController::class)
        ->except(['show', 'destroy'])->names('admin.categorias');
    Route::resource('productos', ProductoController::class)
        ->except(['show', 'destroy'])->names('admin.productos');
    Route::resource('clientes', ClienteController::class)
        ->except(['show', 'destroy'])->names('admin.clientes');

    // Proveedores: el empleado solo ve el listado
    Route::resource('proveedores', ProveedorController::class)
        ->parameters(['proveedores' => 'proveedor'])
        ->only(['index'])->names('admin.proveedores');
    
    // Proveedores: ficha y exportación (lectura para admin y empleado)
    Route::get('/proveedores/exportar', [ProveedorController::class, 'exportar'])->name('admin.proveedores.exportar');
    Route::get('/proveedores/{proveedor}', [ProveedorController::class, 'show'])->whereNumber('proveedor')->name('admin.proveedores.show');   
     
    // Ventas: consultar y cambiar el estado
    Route::get('/ventas', [VentaController::class, 'index'])->name('admin.ventas.index');
    Route::get('/ventas/{venta}', [VentaController::class, 'show'])->name('admin.ventas.show');
    Route::patch('/ventas/{venta}/estado', [VentaController::class, 'estado'])->name('admin.ventas.estado');

    // Compras a proveedores
    Route::get('/compras', [OrdenCompraController::class, 'index'])->name('admin.compras.index');
    Route::get('/compras/crear', [OrdenCompraController::class, 'create'])->name('admin.compras.create');
    Route::post('/compras', [OrdenCompraController::class, 'store'])->name('admin.compras.store');
    Route::get('/compras/{compra}', [OrdenCompraController::class, 'show'])->whereNumber('compra')->name('admin.compras.show');
    Route::get('/compras/{compra}/editar', [OrdenCompraController::class, 'edit'])->whereNumber('compra')->name('admin.compras.edit');
    Route::put('/compras/{compra}', [OrdenCompraController::class, 'update'])->whereNumber('compra')->name('admin.compras.update');
    Route::patch('/compras/{compra}/estado', [OrdenCompraController::class, 'estado'])->whereNumber('compra')->name('admin.compras.estado');

    // Historial de inventario
    Route::get('/inventario', [MovimientoInventarioController::class, 'index'])->name('admin.inventario.index');
    
});

// Solo administrador
Route::middleware(['auth', 'rol:admin'])->prefix('admin')->group(function () {
    Route::resource('categorias', CategoriaController::class)->only(['destroy'])->names('admin.categorias');
    Route::resource('productos', ProductoController::class)->only(['destroy'])->names('admin.productos');
    Route::resource('clientes', ClienteController::class)->only(['destroy'])->names('admin.clientes');

    Route::resource('proveedores', ProveedorController::class)
        ->parameters(['proveedores' => 'proveedor'])
        ->except(['show', 'index'])->names('admin.proveedores');

    Route::resource('usuarios', UsuarioController::class)
        ->except('show')->names('admin.usuarios');
});

// Tienda pública: cualquiera puede ver el catálogo
Route::get('/tienda', [TiendaController::class, 'index'])->name('tienda');

// Cliente: carrito y pedidos
Route::middleware(['auth', 'rol:cliente'])->group(function () {
    Route::get('/carrito', [TiendaController::class, 'carrito'])->name('tienda.carrito');
    Route::post('/carrito/{producto}', [TiendaController::class, 'agregar'])->name('tienda.agregar');
    Route::patch('/carrito/{producto}', [TiendaController::class, 'actualizar'])->name('tienda.actualizar');
    Route::delete('/carrito/{producto}', [TiendaController::class, 'quitar'])->name('tienda.quitar');
    Route::post('/pedido', [TiendaController::class, 'confirmar'])->middleware('throttle:10,1')->name('tienda.confirmar');
    Route::get('/mis-pedidos', [TiendaController::class, 'pedidos'])->name('tienda.pedidos');
    Route::get('/mis-pedidos/{venta}', [TiendaController::class, 'pedido'])->name('tienda.pedido');
    Route::get('/checkout/envio', [TiendaController::class, 'envio'])->name('tienda.envio');
    Route::post('/checkout/envio', [TiendaController::class, 'guardarEnvio'])->name('tienda.envio.guardar');
    Route::get('/checkout/pago', [TiendaController::class, 'pago'])->name('tienda.pago');
    Route::post('/checkout/pago', [TiendaController::class, 'guardarPago'])->name('tienda.pago.guardar');
    Route::get('/checkout/revision', [TiendaController::class, 'revision'])->name('tienda.revision');
    Route::post('/mis-pedidos/{venta}/cancelar', [TiendaController::class, 'cancelar'])->name('tienda.cancelar');
});
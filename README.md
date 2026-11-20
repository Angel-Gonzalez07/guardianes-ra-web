# Guardianes RA

Aplicación web de **Guardianes RA**, un proyecto de Realidad Aumentada que enseña a cuidar el planeta con tres guardianes: **Goti**, **Lumi** y **Tronqui**. Esta web tiene tres partes: una página de presentación, una tienda de figuras coleccionables y un panel de administración de la tienda.

Proyecto integrador del equipo **CAEJA STUDIO**, Universidad Tecnológica del Centro de Veracruz (UTCV), materia Aplicaciones Web.

## Qué incluye

**Página de presentación**
- Conoce a los guardianes, cómo funciona la app de RA y el equipo.
- Muestra productos reales de la tienda.

**Tienda (clientes)**
- Catálogo con búsqueda y filtro por categoría.
- Carrito de compras.
- Compra por pasos: carrito, envío, pago y revisión.
- Pago por transferencia, con los datos de la cuenta en el comprobante.
- Comprobante imprimible con línea de tiempo del pedido.
- Mis pedidos, con cancelación mientras el pedido siga pendiente.

**Panel de administración**
- Panel de control con estadísticas.
- Ventas: consulta, cambio de estado, paquetería y número de guía.
- Compras a proveedores: órdenes de compra con recepción que sube el stock automáticamente.
- Inventario: historial de movimientos (ventas, cancelaciones, compras y ajustes).
- Catálogos: Categorías, Productos, Proveedores, Clientes y Usuarios, con alta, edición, baja, búsqueda y orden.
- Proveedores con datos comerciales, ficha, filtros y exportación a CSV.

## Roles

| Rol | Qué puede hacer |
|---|---|
| Administrador | Todo el sistema, incluidos usuarios y cifras de dinero. |
| Empleado | Panel sin cifras de dinero ni usuarios; ventas, productos, categorías y clientes sin eliminar; proveedores solo lectura. |
| Cliente | Comprar en la tienda y ver sus propios pedidos. |

Los clientes se registran con cualquier correo. El correo `@utcv.edu.mx` es solo para administrador y empleado.

## Seguridad

- Protección CSRF y validación de todos los formularios.
- Límite de intentos en inicio de sesión, registro y compra.
- Contraseñas con mínimo de 8 caracteres, mayúsculas, minúsculas y números.
- Cabeceras de seguridad (CSP, X-Frame-Options, entre otras).
- Cierre automático de sesión para cuentas desactivadas.
- Validación de nombres para evitar datos sin sentido.
- Control de stock con bloqueo de filas para evitar ventas duplicadas.

## Tecnología

- PHP 8.3 o superior y Laravel 13
- PostgreSQL
- Blade para las vistas, con CSS propio

## Instalación

Los pasos completos para correrla en tu computadora están en [INSTALACION.md](INSTALACION.md).

Resumen rápido:

```
git clone https://github.com/Angel-Gonzalez07/guardianes-ra-web.git
cd guardianes-ra-web
composer install
copy .env.example .env
php artisan key:generate
```

Después crea la base de datos con el respaldo incluido y levanta el servidor con `php artisan serve`.

## Cuentas de prueba

| Rol | Correo | Contraseña |
|---|---|---|
| Administrador | caejaestudio@utcv.edu.mx | Admin12345 |

## Equipo

CAEJA STUDIO, UTCV.
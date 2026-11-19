<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php
        $titulos = [
            'dashboard'   => 'Panel de control',
            'categorias'  => 'Categorías',
            'productos'   => 'Productos',
            'proveedores' => 'Proveedores',
            'clientes'    => 'Clientes',
            'usuarios'    => 'Usuarios',
            'ventas'      => 'Ventas',
            'compras'     => 'Compras a proveedores',
            'inventario'  => 'Inventario',
        ];
        $tituloPagina = $titulos[request()->segment(2)] ?? 'Administración';

        $icoPanel = '<rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>';

        // [patrón de ruta activa, ruta del enlace, etiqueta, icono SVG]
        $catalogos = [
            ['admin.categorias.*',  'admin.categorias.index',  'Categorías',  '<path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/>'],
            ['admin.productos.*',   'admin.productos.index',   'Productos',   '<path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/>'],
            ['admin.proveedores.*', 'admin.proveedores.index', 'Proveedores', '<rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>'],
            ['admin.clientes.*',    'admin.clientes.index',    'Clientes',    '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>'],
            ['admin.usuarios.*',    'admin.usuarios.index',    'Usuarios',    '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>'],
        ];

        // El empleado no gestiona usuarios
        if (! auth()->user()->esAdmin()) {
            $catalogos = array_values(array_filter($catalogos, fn ($c) => $c[1] !== 'admin.usuarios.index'));
        }

        $iniciales = mb_strtoupper(mb_substr(auth()->user()->name, 0, 1));
    @endphp
    <title>{{ $tituloPagina }} | Guardianes RA</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,700..800&family=Figtree:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --v900: #123524; --v700: #1F6B40; --v600: #2E7D4F; --v100: #E3EEE2;
            --bg: #EEF2EA; --borde: #CBD6C6; --texto: #17261D; --muted: #55655A;
            --ambar: #FFB300; --rojo: #B3261E;
            --titulo: 'Bricolage Grotesque', 'Figtree', system-ui, sans-serif;
        }
        * { box-sizing: border-box; }
        body { margin: 0; background: var(--bg); color: var(--texto); font: 15px/1.5 'Figtree', system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif; }
        :focus-visible { outline: 3px solid var(--ambar); outline-offset: 2px; }

        /* ---------- Menú lateral ---------- */
        .lateral { position: fixed; top: 0; bottom: 0; left: 0; width: 256px; background: var(--v900); color: #C6D8CB; display: flex; flex-direction: column; z-index: 30; }
        .marca { display: flex; align-items: center; gap: .7rem; padding: 1.1rem 1.2rem; text-decoration: none; color: #fff; }
        .marca strong { display: block; font: 800 1.2rem/1.1 var(--titulo); letter-spacing: -.01em; }
        .marca small { display: block; font-size: .78rem; color: #93B79C; }
        .menu { padding: .3rem .8rem; flex: 1; overflow-y: auto; }
        .menu-tit { margin: 1.3rem .6rem .4rem; font-size: .8rem; font-weight: 700; color: #7FA68A; }
        .menu-item { display: flex; align-items: center; gap: .75rem; width: 100%; padding: .6rem .75rem; border: 0; border-radius: 8px; background: none; color: #D6E4DA; font: 600 .93rem 'Figtree', system-ui, sans-serif; text-decoration: none; cursor: pointer; text-align: left; position: relative; }
        .menu-item svg { width: 18px; height: 18px; flex: none; fill: none; stroke: currentColor; stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round; }
        .menu-item:hover { background: rgba(255, 255, 255, .07); color: #fff; }
        .menu-item.activo { background: rgba(255, 255, 255, .12); color: #fff; }
        .menu-item.activo::before { content: ''; position: absolute; left: -.8rem; top: 20%; bottom: 20%; width: 4px; border-radius: 0 4px 4px 0; background: var(--ambar); }
        .lateral-pie { padding: .8rem; border-top: 1px solid rgba(255, 255, 255, .1); }

        /* ---------- Zona principal ---------- */
        .principal { margin-left: 256px; min-width: 0; min-height: 100vh; display: flex; flex-direction: column; }
        .barra { position: sticky; top: 0; z-index: 20; height: 60px; background: var(--bg); border-bottom: 1px solid var(--borde); display: flex; align-items: center; gap: 1rem; padding: 0 1.8rem; }
        .barra-menu { display: none; border: 1px solid var(--borde); background: #fff; border-radius: 8px; width: 38px; height: 38px; padding: 0; cursor: pointer; color: var(--texto); }
        .barra-menu svg { width: 18px; height: 18px; fill: none; stroke: currentColor; stroke-width: 2; stroke-linecap: round; }
        .migas { color: var(--muted); font-size: .92rem; display: flex; gap: .5rem; align-items: center; }
        .migas strong { color: var(--texto); font-weight: 700; }
        .migas .sep { opacity: .5; }
        .barra-der { margin-left: auto; display: flex; align-items: center; gap: 1.2rem; }
        .fecha { color: var(--muted); font-size: .88rem; }
        .usuario { display: flex; align-items: center; gap: .6rem; }
        .avatar { width: 36px; height: 36px; border-radius: 50%; background: var(--v900); color: var(--ambar); display: grid; place-items: center; font: 800 .95rem var(--titulo); }
        .u-datos strong { display: block; font-size: .9rem; font-weight: 700; line-height: 1.2; }
        .u-datos small { display: block; font-size: .78rem; color: var(--muted); line-height: 1.2; }
        .contenido { padding: 1.8rem; flex: 1; width: 100%; max-width: 1360px; }
        .pie { padding: 1rem 1.8rem; color: var(--muted); font-size: .85rem; }
        .velo { display: none; }

        /* ---------- Alertas ---------- */
        .alerta { border-left: 5px solid; border-radius: 8px; padding: .8rem 1.1rem; margin-bottom: 1.1rem; font-size: .95rem; background: #fff; }
        .alerta.ok { border-left-color: var(--v600); background: #E5F3E6; color: #14501A; }
        .alerta.err { border-left-color: var(--rojo); background: #FBE9E7; color: #7A1812; }
        .alerta ul { margin: .3rem 0 0 1.1rem; padding: 0; }

        @media (max-width: 900px) {
            .lateral { transform: translateX(-100%); transition: transform .2s; }
            .lateral.abierto { transform: none; }
            .velo.visible { display: block; position: fixed; inset: 0; background: rgba(0, 0, 0, .45); z-index: 25; }
            .principal { margin-left: 0; }
            .barra-menu { display: block; }
            .fecha, .u-datos { display: none; }
            .contenido { padding: 1rem; }
            .barra { padding: 0 1rem; }
        }
        @media (prefers-reduced-motion: reduce) { * { transition: none !important; } }
    </style>
</head>
<body>
    <aside class="lateral" id="lateral">
        <a class="marca" href="{{ route('admin.dashboard') }}">
            <svg viewBox="0 0 64 64" width="38" height="38" fill="none" aria-hidden="true">
                <path d="M8 22V10a2 2 0 0 1 2-2h12M42 8h12a2 2 0 0 1 2 2v12M56 42v12a2 2 0 0 1-2 2H42M22 56H10a2 2 0 0 1-2-2V42" stroke="#FFB300" stroke-width="4.5" stroke-linecap="round"/>
                <path d="M32 50C19 48 16 34 22 24c12-1 22 5 22 15 0 6-5 11-12 11z" fill="#A8D84E"/>
                <path d="M32 50C30 42 31 35 38 29" stroke="#123524" stroke-width="2.6" stroke-linecap="round"/>
            </svg>
            <span><strong>Guardianes RA</strong><small>Panel de la tienda</small></span>
        </a>

        <nav class="menu" aria-label="Menú principal">
            <p class="menu-tit">General</p>
            <a class="menu-item {{ request()->routeIs('admin.dashboard') ? 'activo' : '' }}" href="{{ route('admin.dashboard') }}">
                <svg viewBox="0 0 24 24" aria-hidden="true">{!! $icoPanel !!}</svg>Panel de control
            </a>

            <p class="menu-tit">Operación</p>
            <a class="menu-item {{ request()->routeIs('admin.ventas.*') ? 'activo' : '' }}" href="{{ route('admin.ventas.index') }}">
                <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>Ventas
            </a>
            <a class="menu-item {{ request()->routeIs('admin.compras.*') ? 'activo' : '' }}" href="{{ route('admin.compras.index') }}">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M21 8v13H3V8"/><path d="M1 3h22v5H1z"/><path d="M10 12h4"/></svg>Compras
            </a>
            <a class="menu-item {{ request()->routeIs('admin.inventario.*') ? 'activo' : '' }}" href="{{ route('admin.inventario.index') }}">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>Inventario
            </a>
            <p class="menu-tit">Catálogos</p>
            @foreach ($catalogos as [$patron, $ruta, $etiqueta, $icono])
                <a class="menu-item {{ request()->routeIs($patron) ? 'activo' : '' }}" href="{{ route($ruta) }}">
                    <svg viewBox="0 0 24 24" aria-hidden="true">{!! $icono !!}</svg>{{ $etiqueta }}
                </a>
            @endforeach
        </nav>

        <div class="lateral-pie">
            <a class="menu-item" href="{{ route('inicio') }}">
                <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>Ver sitio público
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="menu-item">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>Cerrar sesión
                </button>
            </form>
        </div>
    </aside>
    <div class="velo" id="velo"></div>

    <div class="principal">
        <header class="barra">
            <button type="button" class="barra-menu" id="btnMenu" aria-label="Abrir menú">
                <svg viewBox="0 0 24 24"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
            </button>
            <div class="migas">
                <span>{{ auth()->user()->esAdmin() ? 'Administración' : 'Panel de empleado' }}</span><span class="sep">/</span><strong>{{ $tituloPagina }}</strong>
            </div>
            <div class="barra-der">
                <span class="fecha">{{ now()->translatedFormat('d \d\e F, Y') }}</span>
                <div class="usuario">
                    <span class="avatar">{{ $iniciales }}</span>
                    <span class="u-datos"><strong>{{ auth()->user()->name }}</strong><small>{{ auth()->user()->etiquetaRol() }}</small></span>
                </div>
            </div>
        </header>

        <main class="contenido">
            @if (session('exito'))
                <div class="alerta ok" role="status">{{ session('exito') }}</div>
            @endif
            @if ($errors->any())
                <div class="alerta err" role="alert">
                    <strong>Revisa lo siguiente:</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('contenido')
        </main>

        <footer class="pie">Guardianes RA &middot; CAEJA ESTUDIO &middot; Universidad Tecnológica del Centro de Veracruz</footer>
    </div>

    <script>
        (function () {
            var boton = document.getElementById('btnMenu');
            var velo = document.getElementById('velo');
            var lateral = document.getElementById('lateral');
            function abrir(si) { lateral.classList.toggle('abierto', si); velo.classList.toggle('visible', si); }
            boton.addEventListener('click', function () { abrir(!lateral.classList.contains('abierto')); });
            velo.addEventListener('click', function () { abrir(false); });
        })();
    </script>
</body>
</html>
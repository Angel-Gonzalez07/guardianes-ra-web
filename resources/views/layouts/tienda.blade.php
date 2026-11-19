<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('titulo', 'Tienda') | Guardianes RA</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@600;800&family=Nunito:wght@400;700;800&display=swap" rel="stylesheet">
    <style>
        abel:has(+ :is(input, select, textarea)[required])::after { content: " *"; color: #C62828; font-weight: 700; }
        :root { --verde:#2E7D32; --verde-osc:#1B5E20; --menta:#A5D6A7; --crema:#FFF8E1; --cafe:#5D4037; --ambar:#FFB300; --rojo:#B3261E; --sombra:0 8px 24px rgba(93,64,55,.15); }
        * { box-sizing:border-box; margin:0; padding:0; }
        body { font-family:'Nunito', Arial, sans-serif; background:var(--crema); color:var(--cafe); line-height:1.6; min-height:100vh; display:flex; flex-direction:column; }
        h1, h2, h3, .logo { font-family:'Baloo 2','Nunito',Arial,sans-serif; line-height:1.15; }
        a { color:inherit; text-decoration:none; }
        .nav { background:var(--verde); color:#fff; display:flex; align-items:center; justify-content:space-between; gap:1rem; flex-wrap:wrap; padding:.7rem 5%; }
        .logo { font-size:1.45rem; font-weight:800; }
        .enlaces { display:flex; gap:1.2rem; align-items:center; flex-wrap:wrap; font-weight:700; }
        .enlaces a { white-space:nowrap; }
        .enlaces a:hover { color:var(--ambar); }
        .btn { display:inline-block; padding:.55rem 1.3rem; border-radius:999px; border:2px solid var(--ambar); background:var(--ambar); color:var(--cafe); font:800 .95rem 'Nunito',sans-serif; cursor:pointer; transition:transform .2s; }
        .btn:hover { transform:translateY(-2px); }
        .btn-verde { background:var(--verde); border-color:var(--verde); color:#fff; }
        .btn-borde { background:transparent; border-color:#fff; color:#fff; }
        .btn-rojo { background:#fff; border-color:#E5B9B5; color:var(--rojo); }
        .btn-sm { padding:.3rem .9rem; font-size:.85rem; }
        .cont { width:100%; max-width:1150px; margin:0 auto; padding:2rem 5% 3rem; flex:1; }
        .tit { font-size:clamp(1.8rem,4vw,2.4rem); color:var(--verde); margin-bottom:.3rem; }
        .sub { margin-bottom:1.5rem; }
        .aviso { padding:.7rem 1rem; border-radius:12px; margin-bottom:1rem; font-weight:700; }
        .aviso.ok { background:var(--menta); color:var(--verde-osc); }
        .aviso.err { background:#FDEDEB; color:#7A1812; border:1px solid #F0C4BF; }
        .aviso ul { margin:.3rem 0 0 1.1rem; font-weight:600; }
        .tarjeta { background:#fff; border-radius:20px; box-shadow:var(--sombra); padding:1.4rem; }
        .etiqueta { display:inline-block; background:var(--ambar); padding:.1rem .8rem; border-radius:999px; font-size:.78rem; font-weight:800; }
        input, select, textarea { width:100%; padding:.55rem .7rem; border:1px solid var(--menta); border-radius:10px; font:inherit; background:#fff; color:var(--cafe); }
        input:focus, select:focus, textarea:focus { outline:none; border-color:var(--verde); box-shadow:0 0 0 3px rgba(46,125,50,.18); }
        label { display:block; font-weight:800; margin:.9rem 0 .25rem; font-size:.9rem; }
        table { width:100%; border-collapse:collapse; }
        th { text-align:left; font-size:.75rem; text-transform:uppercase; letter-spacing:.06em; padding:.6rem .7rem; background:#F1F8E9; }
        td { padding:.7rem; border-bottom:1px solid #EFE7CF; vertical-align:middle; }
        .tabla-wrap { overflow-x:auto; }
        footer { background:var(--cafe); color:#fff; text-align:center; padding:1.2rem 5%; font-size:.9rem; }
    </style>
    @stack('estilos')
</head>
<body>
    <header class="nav">
        <a href="{{ route('inicio') }}" class="logo">Guardianes RA</a>
        <nav class="enlaces">
            <a href="{{ route('inicio') }}">Inicio</a>
            <a href="{{ route('tienda') }}">Tienda</a>
            @auth
                @if (auth()->user()->esCliente())
                    <a href="{{ route('tienda.pedidos') }}">Mis pedidos</a>
                    <a href="{{ route('tienda.carrito') }}">Carrito ({{ collect(session('carrito', []))->sum() }})</a>
                @else
                    <a href="{{ route('admin.dashboard') }}">Panel</a>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-borde btn-sm">Salir</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn btn-borde btn-sm">Iniciar sesión</a>
                <a href="{{ route('registro') }}" class="btn btn-sm">Registrarme</a>
            @endauth
        </nav>
    </header>

    <main class="cont">
        @if (session('exito'))
            <div class="aviso ok" role="status">{{ session('exito') }}</div>
        @endif
        @if ($errors->any())
            <div class="aviso err" role="alert">
                <strong>Revisa lo siguiente:</strong>
                <ul>@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif
        @yield('contenido')
    </main>

    <footer>2026 Guardianes RA · CAEJA ESTUDIO · UTCV</footer>
</body>
</html>
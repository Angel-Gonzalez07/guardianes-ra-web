<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('titulo') | Guardianes RA</title>
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@700;800&family=Nunito:wght@400;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0; min-height: 100vh; padding: 1rem;
            display: flex; align-items: center; justify-content: center;
            background: #FFF8E1; color: #5D4037;
            font-family: 'Nunito', system-ui, sans-serif;
        }
        .caja {
            max-width: 460px; width: 100%; text-align: center;
            background: #fff; border-radius: 20px; padding: 2.2rem 1.6rem;
            border-top: 6px solid #2E7D32;
            box-shadow: 0 8px 24px rgba(27, 94, 32, .15);
        }
        .codigo { font-family: 'Baloo 2', sans-serif; font-size: 4.5rem; font-weight: 800; color: #2E7D32; line-height: 1; }
        h1 { font-family: 'Baloo 2', sans-serif; color: #1B5E20; margin: .4rem 0; font-size: 1.6rem; }
        p { margin: .4rem 0 1.4rem; }
        a.btn {
            display: inline-block; padding: .7rem 1.4rem; border-radius: 999px;
            background: #2E7D32; color: #fff; font-weight: 700; text-decoration: none;
        }
        a.btn:hover { background: #1B5E20; }
    </style>
</head>
<body>
    <div class="caja">
        <div class="codigo">@yield('codigo')</div>
        <h1>@yield('titulo')</h1>
        <p>@yield('mensaje')</p>
        <a class="btn" href="{{ url('/') }}">Volver al inicio</a>
    </div>
</body>
</html>
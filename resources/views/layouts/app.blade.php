   <!DOCTYPE html>
   <html lang="es">
   <head>
       <meta charset="UTF-8">
       <meta name="viewport" content="width=device-width, initial-scale=1.0">
       <title>@yield('titulo', 'Guardianes RA')</title>
       <style>
           :root { --verde: #2E7D32; --menta: #A5D6A7; --crema: #FFF8E1; --cafe: #5D4037; --ambar: #FFB300; }
           * { box-sizing: border-box; }
           body { margin: 0; font-family: Arial, sans-serif; background: var(--crema); color: var(--cafe); }
           .barra { background: var(--verde); color: #fff; display: flex; justify-content: space-between; align-items: center; padding: 12px 24px; }
           .barra form { display: flex; align-items: center; gap: 12px; }
           .contenedor { max-width: 900px; margin: 24px auto; padding: 0 16px; }
           .tarjeta { background: #fff; border: 2px solid var(--menta); border-radius: 12px; padding: 24px; }
           .btn { background: var(--verde); color: #fff; border: 0; border-radius: 8px; padding: 10px 18px; cursor: pointer; }
           .btn:hover { background: var(--cafe); }
           .alerta { padding: 10px 14px; border-radius: 8px; margin-bottom: 12px; }
           .exito { background: var(--menta); }
           .error { background: #ffcdd2; color: #b71c1c; }
           input { width: 100%; padding: 10px; margin: 6px 0 12px; border: 1px solid var(--menta); border-radius: 8px; }
       </style>
   </head>
   <body>
       @auth
           <div class="barra">
               <strong>Guardianes RA</strong>
               <form method="POST" action="{{ route('logout') }}">
                   @csrf
                   <span>{{ auth()->user()->name }}</span>
                   <button class="btn" type="submit">Salir</button>
               </form>
           </div>
       @endauth

       <div class="contenedor">
           @if (session('exito'))
               <div class="alerta exito">{{ session('exito') }}</div>
           @endif
           @if ($errors->any())
               <div class="alerta error">
                   @foreach ($errors->all() as $mensaje)
                       <div>{{ $mensaje }}</div>
                   @endforeach
               </div>
           @endif

           @yield('contenido')
       </div>
   </body>
   </html>
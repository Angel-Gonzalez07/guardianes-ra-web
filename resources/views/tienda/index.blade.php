@extends('layouts.tienda')

@section('titulo', 'Tienda')

@push('estilos')
<style>
    .filtros { display:flex; flex-wrap:wrap; gap:.6rem; align-items:center; justify-content:space-between; margin-bottom:1.4rem; }
    .pills { display:flex; flex-wrap:wrap; gap:.5rem; }
    .pill { padding:.35rem 1rem; border-radius:999px; border:2px solid var(--menta); background:#fff; font-weight:800; font-size:.9rem; }
    .pill.on { background:var(--verde); border-color:var(--verde); color:#fff; }
    .buscar { display:flex; gap:.4rem; }
    .buscar input { min-width:220px; }
    .grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(230px, 1fr)); gap:1.4rem; }
    .prod { background:#fff; border-radius:22px; box-shadow:var(--sombra); overflow:hidden; display:flex; flex-direction:column; }
    .prod-img { position:relative; height:230px; overflow:hidden; background:#F3F4F1; }
    .prod-img img { position:absolute; inset:0; width:100%; height:100%; object-fit:contain; padding:.4rem; }
    .prod-img span { position:absolute; inset:0; display:grid; place-items:center; font:800 3.4rem 'Baloo 2',sans-serif; color:var(--verde); opacity:.55; background:linear-gradient(var(--menta), #C8E6C9); }
    .prod-cuerpo { padding:1rem 1.1rem 1.2rem; display:flex; flex-direction:column; gap:.3rem; flex:1; }
    .prod h3 { color:var(--verde); font-size:1.2rem; }
    .prod small { color:#8D6E63; font-weight:700; }
    .precio { font:800 1.4rem 'Baloo 2',sans-serif; }
    .comprar { display:flex; gap:.4rem; margin-top:auto; padding-top:.5rem; }
    .comprar input { width:64px; padding:.4rem; }
    .sin { color:var(--rojo); font-weight:800; font-size:.85rem; }
    .prox { text-align:center; background:#fff; border-radius:20px; padding:2rem 1rem; box-shadow:var(--sombra); }
    .pag { display:flex; gap:.6rem; align-items:center; justify-content:center; margin-top:1.5rem; }
</style>
@endpush

@section('contenido')
    <h1 class="tit">Tienda de guardianes</h1>
    <p class="sub">Figuras coleccionables hoy; peluches y camisas muy pronto.</p>

    <div class="filtros">
        <div class="pills">
            <a class="pill {{ $categoriaId === 0 ? 'on' : '' }}" href="{{ route('tienda', ['q' => $q]) }}">Todo</a>
            @foreach ($categorias as $c)
                <a class="pill {{ $categoriaId === $c->id ? 'on' : '' }}" href="{{ route('tienda', ['categoria' => $c->id, 'q' => $q]) }}">{{ $c->nombre }}</a>
            @endforeach
        </div>
        <form class="buscar" method="GET" action="{{ route('tienda') }}">
            @if ($categoriaId > 0)<input type="hidden" name="categoria" value="{{ $categoriaId }}">@endif
            <input type="text" name="q" value="{{ $q }}" placeholder="Buscar producto o guardián" maxlength="60">
            <button class="btn btn-verde btn-sm" type="submit">Buscar</button>
        </form>
    </div>

    @if ($productos->isEmpty())
        <div class="prox">
            <h3 style="color:var(--verde)">Próximamente</h3>
            <p>{{ $q !== '' ? 'No encontramos productos con esa búsqueda.' : 'Todavía no hay productos en esta categoría. ¡Muy pronto!' }}</p>
        </div>
    @else
        <div class="grid">
            @foreach ($productos as $p)
                <article class="prod">
                    <div class="prod-img">
                        @if ($p->imagen)
                            <img src="{{ asset($p->imagen) }}" alt="{{ $p->nombre }}" loading="lazy">
                        @else
                            <span>{{ mb_substr($p->guardian ?? $p->nombre, 0, 1) }}</span>
                        @endif
                    </div>
                    <div class="prod-cuerpo">
                        <small>{{ $p->categoria->nombre ?? '' }}{{ $p->guardian ? ' · ' . $p->guardian : '' }}</small>
                        <h3>{{ $p->nombre }}</h3>
                        <span class="precio">${{ number_format($p->precio, 2) }}</span>

                        @if ($p->stock < 1)
                            <span class="sin">Agotado</span>
                        @else
                            @guest
                                <a class="btn btn-verde btn-sm" style="margin-top:auto;text-align:center" href="{{ route('login') }}">Inicia sesión para comprar</a>
                            @else
                                @if (auth()->user()->esCliente())
                                    <form class="comprar" method="POST" action="{{ route('tienda.agregar', $p) }}">
                                        @csrf
                                        <input type="number" name="cantidad" value="1" min="1" max="{{ min(10, $p->stock) }}" aria-label="Cantidad">
                                        <button class="btn" type="submit">Agregar al carrito</button>
                                    </form>
                                    @if ($p->stock <= 5)<span class="sin">Últimas {{ $p->stock }} piezas</span>@endif
                                @else
                                    <small>Entra con una cuenta de cliente para comprar.</small>
                                @endif
                            @endguest
                        @endif
                    </div>
                </article>
            @endforeach
        </div>

        @if ($productos->hasPages())
            <div class="pag">
                @if ($productos->previousPageUrl())<a class="btn btn-verde btn-sm" href="{{ $productos->previousPageUrl() }}">← Anterior</a>@endif
                <span>Página {{ $productos->currentPage() }} de {{ $productos->lastPage() }}</span>
                @if ($productos->nextPageUrl())<a class="btn btn-verde btn-sm" href="{{ $productos->nextPageUrl() }}">Siguiente →</a>@endif
            </div>
        @endif
    @endif
@endsection
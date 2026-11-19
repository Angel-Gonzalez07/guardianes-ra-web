@push('estilos')
<style>
    .pasos { display:flex; flex-wrap:wrap; gap:.4rem; list-style:none; margin:0 0 1.6rem; padding:0; counter-reset:paso; }
    .pasos li { counter-increment:paso; display:flex; align-items:center; gap:.5rem; padding:.45rem .9rem; border:2px solid var(--menta); border-radius:999px; background:#fff; font-weight:800; font-size:.88rem; opacity:.7; }
    .pasos li::before { content:counter(paso); display:grid; place-items:center; width:1.5rem; height:1.5rem; border-radius:50%; background:var(--menta); color:var(--verde-osc); font-size:.8rem; }
    .pasos li.hecho { opacity:1; }
    .pasos li.hecho::before { content:'\2713'; background:var(--verde); color:#fff; }
    .pasos li.actual { opacity:1; border-color:var(--verde); background:var(--verde); color:#fff; }
    .pasos li.actual::before { background:#fff; color:var(--verde); }
    .compra { display:grid; grid-template-columns:1.5fr 1fr; gap:1.4rem; align-items:start; }
    @media (max-width:860px) { .compra { grid-template-columns:1fr; } }
    .resumen-c h2 { color:var(--verde); font-size:1.25rem; margin-bottom:.6rem; }
    .resumen-c .linea { display:flex; justify-content:space-between; gap:1rem; padding:.35rem 0; border-bottom:1px solid #EFE7CF; font-size:.95rem; }
    .resumen-c .linea span:first-child { min-width:0; overflow-wrap:anywhere; }
    .resumen-c .total { font:800 1.3rem 'Baloo 2',sans-serif; border-bottom:0; padding-top:.7rem; color:var(--verde-osc); }
    .resumen-c .nota { font-size:.82rem; margin-top:.6rem; }
    .grid2 { display:grid; grid-template-columns:1fr 1fr; gap:0 1rem; }
    @media (max-width:560px) { .grid2 { grid-template-columns:1fr; } }
    .campo-ayuda { display:block; font-size:.8rem; opacity:.8; margin-top:.2rem; }
    .acciones-c { display:flex; flex-wrap:wrap; gap:.6rem; margin-top:1.3rem; }
</style>
@endpush

@php
    $etapas = ['Carrito', 'Envío', 'Pago', 'Revisión'];
@endphp
<ol class="pasos" aria-label="Pasos de la compra">
    @foreach ($etapas as $i => $nombre)
        <li class="{{ $i + 1 < $actual ? 'hecho' : ($i + 1 === $actual ? 'actual' : '') }}">{{ $nombre }}</li>
    @endforeach
</ol>
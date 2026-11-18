<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Guardianes RA | Conoce a tu Guardián</title>
    <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'%3E%3Crect width='64' height='64' rx='14' fill='%232E7D32'/%3E%3Cpath d='M8 22V10a2 2 0 0 1 2-2h12M42 8h12a2 2 0 0 1 2 2v12M56 42v12a2 2 0 0 1-2 2H42M22 56H10a2 2 0 0 1-2-2V42' stroke='%23FFB300' stroke-width='4.5' fill='none' stroke-linecap='round'/%3E%3Cpath d='M32 50C19 48 16 34 22 24c12-1 22 5 22 15 0 6-5 11-12 11z' fill='%23A5D6A7'/%3E%3Cpath d='M32 50C30 42 31 35 38 29' stroke='%232E7D32' stroke-width='2.6' fill='none' stroke-linecap='round'/%3E%3C/svg%3E">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@600;800&family=Nunito:wght@400;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --verde:#2E7D32; --verde-osc:#1B5E20; --menta:#A5D6A7; --crema:#FFF8E1; --cafe:#5D4037; --ambar:#FFB300; --sombra:0 8px 24px rgba(93,64,55,.15); }
        * { box-sizing:border-box; margin:0; padding:0; }
        html { scroll-behavior:smooth; scroll-padding-top:76px; }
        body { font-family:'Nunito', Arial, sans-serif; background:var(--crema); color:var(--cafe); line-height:1.6; overflow-x:hidden; }
        h1, h2, h3, .logo { font-family:'Baloo 2', 'Nunito', Arial, sans-serif; line-height:1.15; }
        a { color:inherit; text-decoration:none; }
        svg { display:block; }

        /* ---------- BARRA SUPERIOR ---------- */
        .nav { position:sticky; top:0; z-index:50; background:rgba(46,125,50,.96); backdrop-filter:blur(6px); color:#fff;
               display:flex; align-items:center; justify-content:space-between; gap:1rem; padding:.7rem 5%; transition:box-shadow .3s; }
        .nav.scrolled { box-shadow:0 4px 18px rgba(0,0,0,.25); }
        .logo { white-space:nowrap; font-size:1.45rem; font-weight:800; display:flex; align-items:center; gap:.4rem; }
        .logo-svg { width:36px; height:36px; flex:none; }
        .enlaces { display:flex; gap:1.3rem; font-weight:700; }
        .enlaces a { position:relative; padding:.2rem 0; white-space:nowrap; }
        .enlaces a::after { content:''; position:absolute; left:0; bottom:-2px; height:3px; width:0; background:var(--ambar); border-radius:2px; transition:width .25s; }
        .enlaces a:hover::after { width:100%; }
        .acciones { display:flex; gap:.5rem; align-items:center; }
        .saludo { font-size:.9rem; font-weight:700; white-space:nowrap; }
        .menu-btn { display:none; background:none; border:0; color:#fff; font-size:1.7rem; cursor:pointer; line-height:1; }

        /* ---------- BOTONES ---------- */
        .btn { white-space:nowrap; display:inline-block; padding:.65rem 1.4rem; border-radius:999px; border:2px solid var(--ambar); background:var(--ambar);
               color:var(--cafe); font-weight:800; cursor:pointer; font-size:1rem; font-family:inherit; transition:transform .2s, box-shadow .2s; }
        .btn:hover { transform:translateY(-2px) scale(1.04); box-shadow:0 6px 16px rgba(255,179,0,.45); }
        .btn-borde { background:transparent; color:#fff; border-color:#fff; }
        .btn-verde { background:var(--verde); border-color:var(--verde); color:#fff; }
        .btn-sm { padding:.4rem 1.05rem; font-size:.9rem; }

        /* ---------- ANIMACIONES BASE ---------- */
        @keyframes flota { 0%, 100% { transform:translateY(0); } 50% { transform:translateY(-10px); } }
        @keyframes entra { from { opacity:0; transform:translateY(28px); } to { opacity:1; transform:none; } }
        @keyframes cae { from { transform:translate3d(0,-40px,0) rotate(0deg); } to { transform:translate3d(var(--dx),820px,0) rotate(540deg); } }
        @keyframes pulso { 0%, 100% { transform:scale(1); opacity:1; } 50% { transform:scale(.94); opacity:.6; } }
        .entra { animation:entra .9s both; }
        .rev { opacity:0; transform:translateY(34px); transition:opacity .7s ease, transform .7s ease; transition-delay:var(--d, 0s); }
        .rev.vis { opacity:1; transform:none; }

        /* ---------- HERO ---------- */
        .hero { position:relative; overflow:hidden; background:linear-gradient(160deg, var(--menta) 0%, #DCEDC8 45%, var(--crema) 100%); padding:5rem 5% 11rem; }
        .hero-in { max-width:820px; margin:0 auto; display:grid; grid-template-columns:1fr; gap:2.5rem; align-items:center; text-align:center; position:relative; z-index:2; }
        .hero-in .btns { justify-content:center; }
        .hero-in p { margin-left:auto; margin-right:auto; }
        .insignia { display:inline-block; background:#fff; color:var(--verde); font-weight:800; padding:.3rem .95rem; border-radius:999px; font-size:.85rem; box-shadow:var(--sombra); margin-bottom:1rem; }
        .hero h1 { font-size:clamp(2.4rem, 6vw, 4rem); color:var(--verde-osc); margin-bottom:1rem; }
        .hero h1 span { color:var(--ambar); text-shadow:0 2px 0 rgba(93,64,55,.2); }
        .hero p { font-size:1.15rem; max-width:520px; margin-bottom:1.8rem; }
        .btns { display:flex; gap:.8rem; flex-wrap:wrap; }
        .escena { position:absolute; left:0; right:0; bottom:-1px; width:100%; height:190px; z-index:1; pointer-events:none; }
        .hoja { position:absolute; top:0; width:22px; height:22px; opacity:.55; animation:cae linear infinite; pointer-events:none; z-index:1; }

        /* Teléfono con demo de RA */
        .telefono { width:min(280px, 78vw); margin:0 auto; background:#263238; border-radius:38px; padding:10px;
                    box-shadow:0 30px 60px rgba(27,94,32,.35), 0 0 0 2px #37474F; animation:flota 6s ease-in-out infinite; }
        .pantalla { position:relative; aspect-ratio:9/18.5; border-radius:30px; overflow:hidden; background:linear-gradient(#607D8B, #37474F); }
        .notch { position:absolute; top:8px; left:50%; width:70px; height:16px; margin-left:-35px; background:#263238; border-radius:10px; z-index:6; }
        .mesa { position:absolute; left:0; right:0; bottom:0; height:36%; background:linear-gradient(#A1887F, #6D4C41); }
        .obj-demo { position:absolute; left:0; right:0; bottom:21%; margin:auto; width:38%; transition:opacity .4s; }
        .g-wrap { position:absolute; left:0; right:0; bottom:35%; margin:auto; width:56%; animation:flota 3s ease-in-out infinite; }
        .g-wrap svg { width:100%; transform:scale(0); transform-origin:50% 100%; transition:transform .6s cubic-bezier(.34, 1.56, .64, 1); }
        .pantalla.on .g-wrap svg { transform:scale(1); }
        .reticula { position:absolute; inset:16% 12% 38%; z-index:4; pointer-events:none; }
        .reticula i { position:absolute; width:26px; height:26px; border:3px solid var(--ambar); transition:border-color .3s; }
        .reticula i:nth-child(1) { top:0; left:0; border-right:0; border-bottom:0; border-radius:8px 0 0 0; }
        .reticula i:nth-child(2) { top:0; right:0; border-left:0; border-bottom:0; border-radius:0 8px 0 0; }
        .reticula i:nth-child(3) { bottom:0; left:0; border-right:0; border-top:0; border-radius:0 0 0 8px; }
        .reticula i:nth-child(4) { bottom:0; right:0; border-left:0; border-top:0; border-radius:0 0 8px 0; }
        .pantalla.scan .reticula { animation:pulso 1s ease-in-out infinite; }
        .pantalla.on .reticula i { border-color:#69F0AE; }
        .estado { position:absolute; top:34px; left:0; right:0; margin:auto; width:max-content; max-width:90%; background:rgba(0,0,0,.45); color:#fff;
                  font-size:.75rem; font-weight:700; padding:.25rem .8rem; border-radius:999px; z-index:5; transition:background .3s; }
        .pantalla.on .estado { background:rgba(46,125,50,.92); }
        .tarjeta-ar { position:absolute; left:12px; right:12px; bottom:12px; background:rgba(255,248,225,.93); border-radius:18px; padding:.6rem .85rem; z-index:5;
                      transform:translateY(140%); transition:transform .5s cubic-bezier(.34, 1.3, .64, 1); box-shadow:0 6px 16px rgba(0,0,0,.3); }
        .pantalla.on .tarjeta-ar { transform:none; }
        .tarjeta-ar strong { display:block; font-family:'Baloo 2', sans-serif; color:var(--verde); font-size:1.05rem; line-height:1.2; }
        .tarjeta-ar span { display:block; font-size:.78rem; line-height:1.3; }

        /* ---------- SECCIONES ---------- */
        section { padding:5rem 5%; }
        .cont { max-width:1150px; margin:0 auto; }
        .tit { text-align:center; font-size:clamp(1.8rem, 4vw, 2.6rem); color:var(--verde); }
        .sub { text-align:center; max-width:640px; margin:.5rem auto 2.8rem; }
        .alt { background:#F1F8E9; }

        /* Cómo funciona */
        .pasos { display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:1.6rem; }
        .paso { background:#fff; border-radius:22px; padding:1.9rem 1.4rem 1.5rem; text-align:center; box-shadow:var(--sombra); position:relative; transition:transform .3s; }
        .paso:hover { transform:translateY(-8px); }
        .paso .num { position:absolute; top:-14px; left:-8px; width:40px; height:40px; border-radius:50%; background:var(--ambar); display:grid; place-items:center;
                     font-family:'Baloo 2', sans-serif; font-weight:800; font-size:1.2rem; color:var(--cafe); box-shadow:var(--sombra); }
        .paso .ico { display:block; margin-bottom:.6rem; transition:transform .3s; }
        .paso .ico svg { display:block; margin:0 auto; width:46px; height:46px; fill:none; stroke:var(--verde); stroke-width:1.7; stroke-linecap:round; stroke-linejoin:round; }
        .paso:hover .ico { transform:scale(1.2) rotate(-8deg); }
        .paso h3 { color:var(--verde); margin-bottom:.3rem; }

        /* Guardianes */
        .guardianes { display:grid; grid-template-columns:repeat(auto-fit, minmax(250px, 1fr)); gap:1.7rem; }
        .gcard { --c:#66BB6A; background:#fff; border-radius:26px; overflow:hidden; box-shadow:var(--sombra); transition:transform .2s ease-out, box-shadow .3s; height:100%; }
        .gcard:hover { box-shadow:0 18px 40px rgba(93,64,55,.25); }
        .gtop { background:var(--menta); background:radial-gradient(circle at 50% 62%, #fff 0, color-mix(in srgb, var(--c) 35%, #fff) 75%); padding:1.4rem 1rem .8rem; display:grid; place-items:center; }
        .gtop .fl { animation:flota 3.2s ease-in-out infinite; }
        .gtop svg { width:150px; transition:transform .35s cubic-bezier(.34, 1.56, .64, 1); }
        .gcard:hover .gtop svg { transform:scale(1.14) rotate(-4deg); }
        .gbody { padding:1rem 1.3rem 1.6rem; text-align:center; }
        .gbody h3 { font-size:1.6rem; color:var(--verde); }
        .rol { font-size:.85rem; font-weight:800; opacity:.8; }
        .vive { display:inline-flex; align-items:center; gap:.4rem; background:var(--crema); border-radius:999px; padding:.2rem .9rem .2rem .3rem; font-weight:800; font-size:.85rem; margin:.7rem 0; }
        .vive svg { width:32px; height:32px; }
        .consejo { background:var(--crema); border-left:5px solid var(--c); border-radius:12px; padding:.7rem .9rem; font-size:.93rem; text-align:left; }

        /* Figuras */
        .figuras { display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:1.5rem; }
        .fig { background:#fff; border-radius:22px; padding:1.3rem; text-align:center; box-shadow:var(--sombra); transition:transform .3s; }
        .fig:hover { transform:translateY(-6px); }
        .peana { background:linear-gradient(var(--menta), #C8E6C9); border-radius:16px; padding:1.1rem 0 .5rem; margin-bottom:.9rem; }
        .peana svg { width:110px; margin:0 auto; filter:drop-shadow(0 6px 6px rgba(0,0,0,.2)); transition:transform .4s; }
        .fig:hover .peana svg { transform:translateY(-6px) rotate(4deg) scale(1.08); }
        .fig h3 { color:var(--verde); margin-bottom:.4rem; }
        .etiqueta { display:inline-block; background:var(--ambar); padding:.15rem .95rem; border-radius:999px; font-size:.8rem; font-weight:800; }
        .peana-img { padding:0; height:170px; display:grid; place-items:center; overflow:hidden; }
        .peana-img img { width:100%; height:100%; object-fit:cover; }
        .inicial { font:800 3.4rem 'Baloo 2',sans-serif; color:var(--verde); opacity:.55; }
        .precio-l { font:800 1.35rem 'Baloo 2',sans-serif; margin:0 0 .7rem; }

        /* App */
        .app { background:linear-gradient(135deg, var(--verde), var(--verde-osc)); color:#fff; }
        .app .cont { display:grid; grid-template-columns:1fr 1fr; gap:2.5rem; align-items:center; }
        .app h2 { font-size:clamp(1.8rem, 4vw, 2.6rem); margin-bottom:.6rem; }
        .chips { display:flex; flex-wrap:wrap; gap:.6rem; margin:1.2rem 0 1.6rem; }
        .chip { background:rgba(255,255,255,.16); border:1px solid rgba(255,255,255,.35); padding:.4rem 1rem; border-radius:999px; font-weight:700; font-size:.9rem; }
        .btn-off { background:rgba(255,255,255,.2); border-color:rgba(255,255,255,.5); color:#fff; cursor:not-allowed; }
        .btn-off:hover { transform:none; box-shadow:none; }
        .coro { display:grid; grid-template-columns:repeat(2, 1fr); gap:1rem; max-width:340px; margin:0 auto; }
        .coro .fl { animation:flota 3s ease-in-out infinite; }
        .coro svg { width:100%; filter:drop-shadow(0 8px 8px rgba(0,0,0,.25)); }

        /* Acerca de y contacto */
        .acerca { max-width:760px; margin:0 auto; text-align:center; }
        .equipo { display:flex; flex-wrap:wrap; gap:.6rem; justify-content:center; margin-top:1.4rem; }
        .equipo span { background:#fff; box-shadow:var(--sombra); padding:.4rem 1rem; border-radius:999px; font-weight:700; font-size:.9rem; }
        .contacto { text-align:center; }
        .redes { display:flex; gap:.8rem; justify-content:center; flex-wrap:wrap; margin-top:1.2rem; }
        .redes a { background:var(--verde); color:#fff; padding:.55rem 1.4rem; border-radius:999px; font-weight:800; transition:transform .2s, background .2s; }
        .redes a:hover { background:var(--cafe); transform:translateY(-3px); }
        .aviso { background:var(--menta); text-align:center; padding:.8rem 5%; font-weight:700; }
        footer { background:var(--cafe); color:#fff; text-align:center; padding:1.3rem 5%; font-size:.9rem; }

        /* ---------- RESPONSIVO ---------- */
        @media (max-width:860px) {
            .hero-in, .app .cont { grid-template-columns:1fr; text-align:center; }
            .hero p { margin-left:auto; margin-right:auto; }
            .btns { justify-content:center; }
            .chips { justify-content:center; }
            .menu-btn { display:block; }
            .enlaces { display:none; position:absolute; top:100%; left:0; right:0; flex-direction:column; gap:.2rem; background:var(--verde-osc); padding:1rem 5%; }
            .nav.abierto .enlaces { display:flex; }
            .saludo { display:none; }
            section { padding:3.8rem 5%; }
        }

        @media (max-width:520px) {
            .nav { gap:.4rem; padding:.6rem 4%; }
            .logo { font-size:.95rem; gap:.25rem; }
            .logo-svg { width:26px; height:26px; }
            .acciones { gap:.3rem; }
            .acciones .btn-sm { padding:.3rem .55rem; font-size:.74rem; border-width:1.5px; }
            .menu-btn { font-size:1.5rem; }
        }

        /* Respeta a quien prefiere menos movimiento */
        @media (prefers-reduced-motion:reduce) {
            * { animation:none !important; transition:none !important; }
            .rev { opacity:1; transform:none; }
        }
        
            /* Imagen real en la tarjeta de guardián */
        .gtop-img { padding:0; overflow:hidden; background:var(--c); }
        .gtop-img img { width:100%; height:230px; object-fit:cover; object-position:var(--pos, 50% 50%); display:block; transform:scale(var(--z, 1)); transform-origin:var(--o, 50% 50%); transition:transform .6s ease; }
        .gcard:hover .gtop-img img { transform:scale(calc(var(--z, 1) * 1.08)); }
    </style>
    <noscript><style>.rev { opacity:1; transform:none; }</style></noscript>
</head>
<body>

    {{-- ====== DIBUJOS (SVG reutilizables): logo, escenario, guardianes y objetos ====== --}}
    <svg width="0" height="0" style="position:absolute" aria-hidden="true">
        <defs>
            <radialGradient id="brillo">
                <stop offset="0" stop-color="#FFF59D" stop-opacity=".9"/>
                <stop offset="1" stop-color="#FFF59D" stop-opacity="0"/>
            </radialGradient>

            {{-- Logo: hoja dentro de marco de RA --}}
            <g id="logo">
                <path d="M8 22V10a2 2 0 0 1 2-2h12M42 8h12a2 2 0 0 1 2 2v12M56 42v12a2 2 0 0 1-2 2H42M22 56H10a2 2 0 0 1-2-2V42" stroke="#FFB300" stroke-width="4.5" fill="none" stroke-linecap="round"/>
                <path d="M32 50C19 48 16 34 22 24c12-1 22 5 22 15 0 6-5 11-12 11z" fill="#A5D6A7"/>
                <path d="M32 50C30 42 31 35 38 29" stroke="#2E7D32" stroke-width="2.6" fill="none" stroke-linecap="round"/>
            </g>

            {{-- Árbol para el escenario --}}
            <g id="arbol">
                <rect x="-6" y="-10" width="12" height="48" rx="4" fill="#8D6E63"/>
                <circle cx="0" cy="-30" r="30" fill="#2E7D32"/>
                <circle cx="-24" cy="-14" r="20" fill="#388E3C"/>
                <circle cx="24" cy="-14" r="20" fill="#388E3C"/>
                <circle cx="0" cy="-48" r="18" fill="#43A047"/>
            </g>

            {{-- Musguín: zorro de musgo y raíces --}}
           
            {{-- Goti: criatura verde oliva con alas de hoja --}}
            <g id="g-goti">
                <ellipse cx="60" cy="113" rx="26" ry="4.5" fill="#000" opacity=".12"/>
                <g fill="#D9A93B" stroke="#A8761F" stroke-width="1">
                    <ellipse cx="26" cy="36" rx="5.5" ry="15" transform="rotate(-62 26 36)"/>
                    <ellipse cx="22" cy="50" rx="5.5" ry="15" transform="rotate(-88 22 50)"/>
                    <ellipse cx="27" cy="63" rx="5.5" ry="14" transform="rotate(-118 27 63)"/>
                    <ellipse cx="94" cy="36" rx="5.5" ry="15" transform="rotate(62 94 36)"/>
                    <ellipse cx="98" cy="50" rx="5.5" ry="15" transform="rotate(88 98 50)"/>
                    <ellipse cx="93" cy="63" rx="5.5" ry="14" transform="rotate(118 93 63)"/>
                </g>
                <path d="M44 84C40 96 46 108 54 110L66 110C74 108 80 96 76 84Z" fill="#C49A2C"/>
                <ellipse cx="60" cy="94" rx="10" ry="12" fill="#E3C466"/>
                <path d="M44 88c-8 4-10 12-8 18M76 88c8 4 10 12 8 18" stroke="#B08A24" stroke-width="5" fill="none" stroke-linecap="round"/>
                <ellipse cx="52" cy="110" rx="7" ry="3.5" fill="#8B6B1F"/><ellipse cx="68" cy="110" rx="7" ry="3.5" fill="#8B6B1F"/>
                <ellipse cx="60" cy="54" rx="33" ry="31" fill="#9C9B3F"/>
                <ellipse cx="48" cy="40" rx="18" ry="10" fill="#C2C068" opacity=".6"/>
                <ellipse cx="47" cy="56" rx="7.5" ry="10" fill="#111"><animate attributeName="ry" values="10;10;1;10" keyTimes="0;.92;.96;1" dur="4.5s" repeatCount="indefinite"/></ellipse>
                <ellipse cx="73" cy="56" rx="7.5" ry="10" fill="#111"><animate attributeName="ry" values="10;10;1;10" keyTimes="0;.92;.96;1" dur="4.5s" repeatCount="indefinite"/></ellipse>
                <circle cx="50" cy="51" r="2.4" fill="#fff"/><circle cx="76" cy="51" r="2.4" fill="#fff"/>
                <path d="M55 72q5 4 10 0" stroke="#5E5D22" stroke-width="2.2" fill="none" stroke-linecap="round"/>
                <circle cx="22" cy="22" r="2.2" fill="#E040FB"><animate attributeName="opacity" values=".2;1;.2" dur="2s" repeatCount="indefinite"/></circle>
                <circle cx="100" cy="24" r="2.6" fill="#E040FB"><animate attributeName="opacity" values="1;.2;1" dur="2.4s" repeatCount="indefinite"/></circle>
            </g>

            {{-- Lumi: mago de la luz --}}
            <g id="g-lumi">
                <circle cx="60" cy="64" r="58" fill="url(#brillo)" opacity=".55"><animate attributeName="r" values="52;60;52" dur="3s" repeatCount="indefinite"/></circle>
                <ellipse cx="60" cy="113" rx="28" ry="4.5" fill="#000" opacity=".14"/>
                <path d="M30 112L38 62Q60 54 82 62L90 112Z" fill="#5C2D55"/>
                <path d="M28 108Q24 76 42 60L54 64L47 108Z" fill="#546E7A"/>
                <path d="M92 108Q96 76 78 60L66 64L73 108Z" fill="#546E7A"/>
                <rect x="43" y="88" width="34" height="6" rx="2" fill="#5D4037"/>
                <rect x="56" y="86.5" width="8" height="9" rx="2" fill="none" stroke="#FFB300" stroke-width="2"/>
                <path d="M38 58Q60 52 82 58Q86 84 60 104Q34 84 38 58Z" fill="#E6E6E6"/>
                <path d="M48 70Q52 86 60 96M72 70Q68 86 60 96M60 66V92" stroke="#BDBDBD" stroke-width="2" fill="none" stroke-linecap="round"/>
                <circle cx="60" cy="49" r="11" fill="#F1C9A5"/>
                <circle cx="60" cy="54" r="4.5" fill="#E7AE86"/>
                <path d="M47 47Q54 43 58 46M62 46Q66 43 73 47" stroke="#fff" stroke-width="3.5" fill="none" stroke-linecap="round"/>
                <circle cx="54" cy="49" r="1.8" fill="#3E2723"/><circle cx="66" cy="49" r="1.8" fill="#3E2723"/>
                <ellipse cx="60" cy="40" rx="35" ry="8" fill="#6D3F1E"/>
                <path d="M38 40L50 18Q56 2 70 4Q62 10 68 22L82 40Z" fill="#8D5A2B"/>
                <path d="M40 38Q60 46 80 38L82 34Q60 42 38 33Z" fill="#FFB300"/>
                <circle cx="62" cy="26" r="2.4" fill="#FFE082"/><circle cx="55" cy="16" r="1.8" fill="#FFE082"/>
                <circle cx="38" cy="90" r="6" fill="#F1C9A5"/>
                <circle cx="84" cy="88" r="6" fill="#F1C9A5"/>
                <circle cx="88" cy="78" r="7" fill="#FFF59D"><animate attributeName="r" values="6;8.5;6" dur="2s" repeatCount="indefinite"/></circle>
                <circle cx="88" cy="78" r="3.5" fill="#fff"/>
                <ellipse cx="50" cy="111" rx="8" ry="3.5" fill="#3E2723"/><ellipse cx="70" cy="111" rx="8" ry="3.5" fill="#3E2723"/>
            </g>

            {{-- Tronqui: gólem de tronco naranja con hongos --}}
            <g id="g-tronqui">
                <ellipse cx="60" cy="113" rx="34" ry="5" fill="#000" opacity=".14"/>
                <g stroke="#C9623C" stroke-width="9" stroke-linecap="round" fill="none">
                    <path d="M36 66L22 82L26 94"/>
                    <path d="M86 66L100 82L96 94"/>
                </g>
                <g fill="#E8673A">
                    <path d="M26 102h30l2 10H24z"/><path d="M64 102h30l2 10H62z"/>
                </g>
                <path d="M34 106v6M42 106v6M50 106v6M72 106v6M80 106v6M88 106v6" stroke="#C4502A" stroke-width="1.8"/>
                <rect x="32" y="40" width="58" height="66" rx="10" fill="#EE6B3B"/>
                <path d="M44 48v22M56 46v14M72 50v18M80 84v16M46 90v10" stroke="#C4502A" stroke-width="1.6" stroke-linecap="round" fill="none"/>
                <g fill="#9CCC3C">
                    <ellipse cx="38" cy="50" rx="6" ry="10"/><ellipse cx="86" cy="76" rx="5" ry="12"/><ellipse cx="62" cy="103" rx="10" ry="3.5"/><ellipse cx="40" cy="92" rx="3.5" ry="5"/>
                </g>
                <ellipse cx="47" cy="72" rx="9" ry="12" fill="#5D2A1A"/>
                <ellipse cx="74" cy="70" rx="9" ry="12" fill="#5D2A1A"/>
                <rect x="45" y="65" width="4" height="13" rx="2" fill="#fff"/>
                <rect x="72" y="63" width="4" height="13" rx="2" fill="#fff"/>
                <g>
                    <rect x="98" y="62" width="5" height="9" rx="2" fill="#F3E5AB"/>
                    <path d="M92 64C92 52 110 52 109 64Z" fill="#EF5350"/>
                    <circle cx="97" cy="58" r="1.6" fill="#fff"/><circle cx="104" cy="59" r="1.4" fill="#fff"/>
                </g>
                <rect x="30" y="36" width="9" height="8" rx="2" fill="#FFB300"/>
                <path d="M26 40C26 24 44 22 48 38Z" fill="#EF5350"/>
                <circle cx="35" cy="31" r="2" fill="#fff"/><circle cx="42" cy="34" r="1.6" fill="#fff"/>
                <path d="M40 40C42 12 98 12 94 44Z" fill="#EF5350"/>
                <circle cx="56" cy="26" r="5.5" fill="#fff"/><circle cx="75" cy="22" r="4.5" fill="#fff"/><circle cx="86" cy="34" r="4" fill="#fff"/><circle cx="64" cy="37" r="3" fill="#fff"/>
                <path d="M52 12L66 4L70 11L56 17Z" fill="#8BC34A"/>
            </g>

            {{-- Objetos cotidianos --}}
            <g id="o-musguin">
                <ellipse cx="60" cy="108" rx="34" ry="5" fill="#000" opacity=".15"/>
                <path d="M92 62c22 0 22 26-4 28" stroke="#A9704F" stroke-width="7" fill="none" stroke-linecap="round"/>
                <path d="M32 54H88L80 106H40Z" fill="#C98F6B"/>
                <rect x="26" y="44" width="68" height="14" rx="5" fill="#A9704F"/>
                <path d="M36 72H84" stroke="#A9704F" stroke-width="4" stroke-linecap="round"/>
                <circle cx="48" cy="86" r="3" fill="#A9704F"/><circle cx="62" cy="90" r="3" fill="#A9704F"/><circle cx="74" cy="84" r="3" fill="#A9704F"/>
            </g>
            <g id="o-goti">
                <ellipse cx="58" cy="108" rx="36" ry="5" fill="#000" opacity=".15"/>
                <path d="M74 74L104 42" stroke="#4DB6AC" stroke-width="9" stroke-linecap="round"/>
                <ellipse cx="106" cy="38" rx="9" ry="14" fill="#00897B" transform="rotate(40 106 38)"/>
                <path d="M38 58C38 32 70 32 70 58" stroke="#4DB6AC" stroke-width="7" fill="none" stroke-linecap="round"/>
                <path d="M30 58C0 56 4 92 30 92" stroke="#4DB6AC" stroke-width="7" fill="none" stroke-linecap="round"/>
                <rect x="28" y="54" width="48" height="52" rx="12" fill="#4DB6AC"/>
                <rect x="34" y="62" width="14" height="30" rx="6" fill="#80CBC4" opacity=".7"/>
            </g>
            <g id="o-lumi">
                <ellipse cx="60" cy="110" rx="26" ry="4" fill="#000" opacity=".15"/>
                <rect x="40" y="50" width="32" height="58" rx="9" fill="#546E7A"/>
                <path d="M30 22H82L74 52H38Z" fill="#FFB300"/>
                <ellipse cx="56" cy="22" rx="26" ry="7" fill="#FFF59D"/>
                <circle cx="56" cy="74" r="5" fill="#FFB300"/>
                <path d="M46 88H66M46 96H66" stroke="#78909C" stroke-width="3" stroke-linecap="round"/>
            </g>
            <g id="o-tronqui">
                <ellipse cx="58" cy="106" rx="42" ry="5" fill="#000" opacity=".15"/>
                <rect x="12" y="46" width="78" height="56" rx="12" fill="#8D6E63"/>
                <path d="M24 56H70M20 74H74M26 90H68" stroke="#6D4C41" stroke-width="3" stroke-linecap="round"/>
                <rect x="40" y="36" width="12" height="14" rx="4" fill="#6D4C41"/>
                <ellipse cx="90" cy="74" rx="16" ry="28" fill="#A1887F"/>
                <ellipse cx="90" cy="74" rx="11" ry="20" fill="none" stroke="#6D4C41" stroke-width="2.5"/>
                <ellipse cx="90" cy="74" rx="5" ry="10" fill="none" stroke="#6D4C41" stroke-width="2.5"/>
            </g>
        </defs>
    </svg>

    {{-- ====== BARRA SUPERIOR ====== --}}
    <header class="nav" id="nav">
        <a href="#inicio" class="logo"><svg class="logo-svg" viewBox="0 0 64 64"><use href="#logo"/></svg> Guardianes RA</a>

        <nav class="enlaces">
            <a href="#inicio">Inicio</a>
            <a href="#como">Cómo funciona</a>
            <a href="#guardianes">Guardianes</a>
            <a href="#figuras">Tienda</a>
            <a href="#app">La app</a>
            <a href="#acerca">Acerca de</a>
            <a href="#contacto">Contacto</a>
        </nav>

        <div class="acciones">
            @auth
                <span class="saludo">Hola, {{ Auth::user()->name }}</span>
                @if (Auth::user()->esPersonal())
                <a class="btn btn-borde btn-sm" href="{{ route('admin.dashboard') }}">Panel</a>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-borde btn-sm">Salir</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn btn-borde btn-sm">Iniciar sesión</a>
                <a href="{{ Route::has('registro') ? route('registro') : '#' }}" class="btn btn-sm">Registrarme</a>
            @endauth
            <button class="menu-btn" id="menuBtn" aria-label="Abrir menú">☰</button>
        </div>
    </header>

    @if (session('exito'))
        <div class="aviso">{{ session('exito') }}</div>
    @endif

    {{-- ====== INICIO ====== --}}
    <section id="inicio" class="hero">
        {{-- Escenario: colinas, árboles y arroyo --}}
        <svg class="escena" viewBox="0 0 1440 220" preserveAspectRatio="xMidYMax slice" aria-hidden="true">
            <path d="M0 110C200 50 420 60 640 100S1060 140 1440 70V220H0Z" fill="#C8E6C9"/>
            <use href="#arbol" transform="translate(330 96) scale(.9)"/>
            <use href="#arbol" transform="translate(1340 112) scale(1.4)"/>
            <path d="M0 145C240 95 480 115 720 145S1200 165 1440 115V220H0Z" fill="#A5D6A7"/>
            <path transform="translate(-380 0)" d="M930 220C945 196 900 182 930 160S1000 150 990 128" stroke="#81D4FA" stroke-width="14" fill="none" stroke-linecap="round" opacity=".85"/>
            <use href="#arbol" transform="translate(760 150) scale(.7)"/>
            <path d="M0 182C260 148 520 168 800 188S1260 198 1440 168V220H0Z" fill="#81C784"/>
            <path d="M0 205C300 180 600 196 900 206S1300 208 1440 196V220H0Z" fill="#FFF8E1"/>
        </svg>

        <div class="hero-in">
            <div>
                <span class="insignia entra"> App de Realidad Aumentada</span>
                <h1 class="entra" style="animation-delay:.1s">Conoce a tu <span>Guardián</span></h1>
                <p class="entra" style="animation-delay:.25s">Apunta tu cámara a un objeto de todos los días y mira cómo <strong>Musguín, Goti, Lumi y Tronqui</strong> aparecen en 3D para contarte cómo cuidar el planeta.</p>
                <div class="btns entra" style="animation-delay:.4s">
                    @guest
                        <a href="{{ Route::has('registro') ? route('registro') : '#' }}" class="btn">Crear mi cuenta</a>
                        <a href="{{ route('login') }}" class="btn btn-verde">Iniciar sesión</a>
                    @else
                        <a href="#guardianes" class="btn">Conocer a los guardianes</a>
                        <a href="#como" class="btn btn-verde">¿Cómo funciona?</a>
                    @endguest
                </div>
            </div>
        </div>
    </section>

    {{-- ====== CÓMO FUNCIONA ====== --}}
    <section id="como">
        <div class="cont">
            <h2 class="tit rev">¿Cómo funciona?</h2>
            <p class="sub rev">Cuatro pasos y un guardián aparece frente a ti. No necesitas crear cuenta para usar la app.</p>
           <div class="pasos">
            <div class="paso">
                <span class="num">1</span>
                <span class="ico"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg></span>
                <h3>Abre la app</h3>
                <p>Pasa la pantalla de inicio y entra a «Cámara AR» desde el menú principal. Acepta el aviso de seguridad.</p>
            </div>

            <div class="paso">
                <span class="num">2</span>
                <span class="ico"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="4"/><line x1="12" y1="1" x2="12" y2="5"/><line x1="12" y1="19" x2="12" y2="23"/><line x1="1" y1="12" x2="5" y2="12"/><line x1="19" y1="12" x2="23" y2="12"/></svg></span>
                <h3>Apunta a tu objeto</h3>
                <p>Enfoca una taza, una mini linterna, una grapadora o una calculadora científica. Cada objeto pertenece a un guardián.</p>
            </div>

            <div class="paso">
                <span class="num">3</span>
                <span class="ico"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg></span>
                <h3>Aparece tu guardián</h3>
                <p>Cuando la app reconoce el objeto, el guardián se muestra en 3D encima de él.</p>
            </div>

            <div class="paso">
                <span class="num">4</span>
                <span class="ico"><svg viewBox="0 0 24 24" aria-hidden="true"><polyline points="15 3 21 3 21 9"/><polyline points="9 21 3 21 3 15"/><line x1="21" y1="3" x2="14" y2="10"/><line x1="3" y1="21" x2="10" y2="14"/></svg></span>
                <h3>Gíralo y agrándalo</h3>
                <p>Con tus dedos gira y cambia el tamaño del guardián, y lee su mensaje para cuidar el ecosistema.</p>
            </div>
        </div>
    </section>

    {{-- ====== GUARDIANES ====== --}}
    <section id="guardianes" class="alt">
        <div class="cont">
            <h2 class="tit rev">Los Guardianes</h2>
            <p class="sub rev">Cada uno cuida una parte del ecosistema y vive en un objeto cotidiano. Pasa el mouse sobre ellos.</p>
            <div class="guardianes">

                <div class="rev" style="--d:.12s">
                    <article class="gcard" style="--c:#4FC3F7">
                        <div class="gtop gtop-img" style="--pos:50% 50%; --z:2.2; --o:51% 44%"><img src="{{ asset('img/guardianes/goti.jpeg') }}" alt="Goti" loading="lazy"></div>
                        <div class="gbody">
                            <h3>Goti</h3>
                            <p class="rol">Espíritu del Agua</p>
                            <span class="vive"><svg viewBox="0 0 120 120"><use href="#o-goti"/></svg>Regadera</span>
                            <p class="consejo">Cierra la llave mientras te cepillas: cada gota cuenta.</p>
                        </div>
                    </article>
                </div>

                <div class="rev" style="--d:.24s">
                    <article class="gcard" style="--c:#FFCA28">
                        <div class="gtop gtop-img" style="--pos:47% 50%; --z:1.7; --o:48% 30%"><img src="{{ asset('img/guardianes/lumi.jpeg') }}" alt="Lumi" loading="lazy"></div>
                        <div class="gbody">
                            <h3>Lumi</h3>
                            <p class="rol">Espíritu del Aire y la Luz</p>
                            <span class="vive"><svg viewBox="0 0 120 120"><use href="#o-lumi"/></svg>Linterna</span>
                            <p class="consejo">Apaga las luces que no uses y ahorra energía.</p>
                        </div>
                    </article>
                </div>

                <div class="rev" style="--d:.36s">
                    <article class="gcard" style="--c:#A1887F">
                        <div class="gtop gtop-img" style="--pos:50% 40%; --z:1.1; --o:50% 50%"><img src="{{ asset('img/guardianes/tronqui.jpeg') }}" alt="Tronqui" loading="lazy"></div>
                        <div class="gbody">
                            <h3>Tronqui</h3>
                            <p class="rol">Guardián Mayor</p>
                            <span class="vive"><svg viewBox="0 0 120 120"><use href="#o-tronqui"/></svg>Leño pequeño</span>
                            <p class="consejo">Reutiliza el papel y cuida los árboles.</p>
                        </div>
                    </article>
                </div>

            </div>
        </div>
    </section>

    {{-- ====== TIENDA (productos reales de la base de datos) ====== --}}
    <section id="figuras">
        <div class="cont">
            <h2 class="tit rev">Tienda de guardianes</h2>
            <p class="sub rev">Lleva a tu guardián favorito a casa.</p>

            @if ($destacados->isEmpty())
                <p class="sub rev">Estamos preparando los primeros productos.</p>
            @else
                <div class="figuras">
                    @foreach ($destacados as $p)
                        <article class="fig rev" style="--d:{{ $loop->index * .1 }}s">
                            <div class="peana peana-img">
                                @if ($p->imagen)
                                    <img src="{{ asset($p->imagen) }}" alt="{{ $p->nombre }}" loading="lazy">
                                @else
                                    <span class="inicial">{{ mb_substr($p->guardian ?? $p->nombre, 0, 1) }}</span>
                                @endif
                            </div>
                            <h3>{{ $p->nombre }}</h3>
                            <p class="precio-l">${{ number_format($p->precio, 2) }}</p>
                            <a class="btn" href="{{ route('tienda') }}">Ver en la tienda</a>
                        </article>
                    @endforeach
                </div>
            @endif

            <p style="text-align:center;margin-top:2rem">
                <a class="btn btn-verde" href="{{ route('tienda') }}">Ver toda la tienda</a>
            </p>
        </div>
    </section>

    {{-- ====== LA APP ====== --}}
    <section id="app" class="app">
        <div class="cont">
            <div class="rev">
                <h2>Lleva a los guardianes en tu celular</h2>
                <p>Una experiencia sencilla, estable y pensada para que aprendas jugando. Sin registros, sin complicaciones.</p>
                <div class="chips">
                    <span class="chip"> Android 7.0 o superior</span>
                    <span class="chip"> Realidad Aumentada</span>
                    <span class="chip"> Sin registro</span>
                    <span class="chip"> 4 guardianes en 3D</span>
                </div>
                <span class="btn btn-off" aria-disabled="true">Descarga disponible próximamente</span>
            </div>
            <div class="coro rev" style="--d:.15s">
                <div class="fl"><svg viewBox="0 0 120 120"><use href="#g-musguin"/></svg></div>
                <div class="fl" style="animation-delay:-.7s"><svg viewBox="0 0 120 120"><use href="#g-goti"/></svg></div>
                <div class="fl" style="animation-delay:-1.4s"><svg viewBox="0 0 120 120"><use href="#g-lumi"/></svg></div>
                <div class="fl" style="animation-delay:-2.1s"><svg viewBox="0 0 120 120"><use href="#g-tronqui"/></svg></div>
            </div>
        </div>
    </section>

    {{-- ====== ACERCA DE ====== --}}
    <section id="acerca">
        <div class="cont">
            <h2 class="tit rev">Acerca de</h2>
            <div class="acerca rev">
                <p>Guardianes RA es el proyecto integrador del equipo <strong> CAEJA STUDIO</strong>, de la Universidad Tecnológica del Centro de Veracruz (UTCV). Diseñamos experiencias digitales que combinan narrativa, tecnología 3D y Realidad Aumentada para comunicar ideas de manera visual y participativa.</p>
                <div class="equipo">
                    <span>Angel Gabriel González Ubaldo</span>
                    <span>José Antonio Pérez Soto</span>
                    <span>Luis Emilio Sevilla Gasca</span>
                    <span>Cesar Hernández Villagómez</span>
                    <span>Aleidy Iasoret Gómez Estrada</span>
                </div>
            </div>
        </div>
    </section>

    {{-- ====== CONTACTO Y REDES ====== --}}
    <section id="contacto" class="alt contacto">
        <div class="cont">
            <h2 class="tit rev">Contacto y redes</h2>
            {{--  AQUÍ VA TU CORREO: cambia el texto después de "Escríbenos:" --}}
            <p class="sub rev" style="margin-bottom:0">Escríbenos: 20243l601129@utcv.edu.mx</p>
            {{-- AQUÍ VAN TUS REDES: cambia cada href="#" por el enlace real cuando existan --}}
            <div class="redes rev">
                <a href="https://www.facebook.com/profile.php?id=61596851962610">Facebook</a>
                <a href="#">Instagram</a>
                <a href="#">TikTok</a>
            </div>
        </div>
    </section>

    <footer><svg class="logo-svg" viewBox="0 0 64 64" style="margin:0 auto .4rem"><use href="#logo"/></svg> 2026 Guardianes RA · Raíces Creativas · UTCV</footer>

    <script>
        (function () {
            var reducir = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

            // Menú móvil y sombra de la barra
            var nav = document.getElementById('nav');
            document.getElementById('menuBtn').addEventListener('click', function () { nav.classList.toggle('abierto'); });
            nav.querySelectorAll('.enlaces a').forEach(function (a) { a.addEventListener('click', function () { nav.classList.remove('abierto'); }); });
            window.addEventListener('scroll', function () { nav.classList.toggle('scrolled', window.scrollY > 10); }, { passive: true });

            // Aparecer al hacer scroll
            var revs = document.querySelectorAll('.rev');
            if ('IntersectionObserver' in window) {
                var io = new IntersectionObserver(function (items) {
                    items.forEach(function (it) { if (it.isIntersecting) { it.target.classList.add('vis'); io.unobserve(it.target); } });
                }, { threshold: 0.15 });
                revs.forEach(function (el) { io.observe(el); });
            } else { revs.forEach(function (el) { el.classList.add('vis'); }); }

            // Hojas que caen en el hero
            if (!reducir) {
                var hero = document.getElementById('inicio');
                var colores = ['#2E7D32', '#66BB6A', '#FFB300', '#81C784'];
                for (var i = 0; i < 14; i++) {
                    var h = document.createElement('div');
                    h.className = 'hoja';
                    h.style.left = (Math.random() * 100) + '%';
                    h.style.color = colores[i % colores.length];
                    h.style.setProperty('--dx', (Math.random() * 160 - 80) + 'px');
                    h.style.animationDuration = (9 + Math.random() * 8) + 's';
                    h.style.animationDelay = (-Math.random() * 14) + 's';
                    h.innerHTML = '<svg viewBox="0 0 24 24"><path d="M3 21C3 9 11 3 21 3c0 10-6 18-18 18z" fill="currentColor"/></svg>';
                    hero.appendChild(h);
                }
            }

            // Inclinación 3D suave en las tarjetas de guardianes
            if (!reducir) {
                document.querySelectorAll('.gcard').forEach(function (c) {
                    c.addEventListener('mousemove', function (e) {
                        var r = c.getBoundingClientRect();
                        var x = (e.clientX - r.left) / r.width - 0.5;
                        var y = (e.clientY - r.top) / r.height - 0.5;
                        c.style.transform = 'perspective(700px) rotateY(' + (x * 10) + 'deg) rotateX(' + (-y * 10) + 'deg) translateY(-6px)';
                    });
                    c.addEventListener('mouseleave', function () { c.style.transform = ''; });
                });
            }
        })();
    </script>
</body>
</html>
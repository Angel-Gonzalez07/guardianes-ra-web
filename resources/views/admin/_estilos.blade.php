<style>
    /* Encabezado de página */
    .adm-titulo { margin: 0 0 1rem; font: 800 1.9rem/1.1 var(--titulo); color: var(--texto); letter-spacing: -.02em; }

    /* Barra de herramientas: búsqueda y acciones */
    .adm-barra { display: flex; flex-wrap: wrap; gap: .7rem; align-items: center; justify-content: space-between; margin: 0 0 1rem; }
    .adm-buscar { display: flex; gap: .5rem; flex-wrap: wrap; }
    .adm-buscar input { padding: .6rem .8rem; border: 1px solid var(--borde); border-radius: 8px; font: inherit; font-size: .92rem; min-width: 280px; background: #fff; }
    .adm-buscar input:focus { outline: none; border-color: var(--v600); box-shadow: 0 0 0 3px rgba(46, 125, 79, .2); }

    /* Botones */
    .adm-btn { display: inline-block; padding: .6rem 1rem; border: 1.5px solid var(--v900); border-radius: 8px; background: var(--v900); color: #fff; font: 700 .9rem/1.2 'Figtree', system-ui, sans-serif; text-decoration: none; cursor: pointer; transition: background .15s, border-color .15s; }
    .adm-btn:hover { background: var(--v700); border-color: var(--v700); }
    .adm-btn.sec { background: transparent; color: var(--texto); border-color: #9DB09F; }
    .adm-btn.sec:hover { background: rgba(18, 53, 36, .07); border-color: var(--v900); }
    .adm-btn.rojo { background: transparent; color: var(--rojo); border-color: #DDA8A3; }
    .adm-btn.rojo:hover { background: var(--rojo); color: #fff; border-color: var(--rojo); }
    .adm-btn.chico { padding: .35rem .75rem; font-size: .82rem; }

    /* Paneles */
    .panel { background: #fff; border: 1px solid var(--borde); border-radius: 12px; overflow: hidden; }
    .panel-cab { display: flex; align-items: center; justify-content: space-between; padding: .9rem 1.1rem; border-bottom: 1px solid var(--borde); }
    .panel-cab h2 { margin: 0; font: 800 1.1rem var(--titulo); letter-spacing: -.01em; }
    .panel-cab a { font-size: .88rem; color: var(--v700); font-weight: 700; text-decoration: none; }
    .panel-cab a:hover { text-decoration: underline; }
    .panel-cuerpo { padding: 1.1rem; }

    /* Tablas */
    .adm-tabla-wrap { overflow-x: auto; background: #fff; border: 1px solid var(--borde); border-radius: 12px; }
    .adm-tabla { width: 100%; border-collapse: collapse; font-size: .93rem; }
    .adm-tabla th { background: var(--v100); color: #2C4636; text-align: left; padding: .75rem 1rem; font-size: .85rem; font-weight: 700; white-space: nowrap; border-bottom: 1px solid var(--borde); }
    .adm-tabla th a { color: inherit; text-decoration: none; }
    .adm-tabla th a:hover { color: var(--v700); text-decoration: underline; }
    .adm-tabla td { padding: .7rem 1rem; border-bottom: 1px solid #E4EAE0; vertical-align: middle; }
    .adm-tabla tbody tr:last-child td { border-bottom: 0; }
    .adm-tabla tbody tr:hover td { background: #F5F8F2; }
    .adm-tabla td strong { font-weight: 700; }
    .adm-tabla td a { color: inherit; }
    .adm-acciones { display: flex; gap: .4rem; flex-wrap: nowrap; }
    .adm-vacio { text-align: center; padding: 2.2rem 1rem !important; color: var(--muted); }
    .adm-sinimg { display: inline-block; width: 46px; padding: .35rem 0; border: 1.5px dashed #9DB09F; border-radius: 6px; text-align: center; font-size: .62rem; line-height: 1.1; color: var(--muted); }

    /* Etiquetas de estado */
    .adm-badge { display: inline-block; padding: .12rem .6rem; border: 1px solid; border-radius: 99px; font-size: .78rem; font-weight: 700; }
    .adm-badge.si { background: #E3F2E5; color: #1B5E20; border-color: #B7D9BB; }
    .adm-badge.no { background: #FBE9E7; color: #A3261E; border-color: #F1C6C1; }

    /* Paginación */
    .adm-pag { display: flex; gap: .6rem; align-items: center; justify-content: flex-end; margin: 1rem 0 0; color: var(--muted); font-size: .9rem; }

    /* Formularios */
    .adm-form { max-width: 640px; background: #fff; border: 1px solid var(--borde); border-radius: 12px; padding: 1.6rem 1.7rem 1.7rem; }
    .adm-form .adm-titulo { padding-bottom: .9rem; border-bottom: 1px solid var(--borde); margin-bottom: .4rem; }
    .adm-form label { display: block; font-size: .88rem; font-weight: 700; color: #2C4636; margin: 1.1rem 0 .35rem; }
    .adm-form input[type=text], .adm-form input[type=email], .adm-form input[type=number], .adm-form input[type=password], .adm-form input[type=file], .adm-form select, .adm-form textarea {
        width: 100%; padding: .65rem .8rem; border: 1px solid var(--borde); border-radius: 8px; font: inherit; font-size: .95rem; background: #fff; color: var(--texto);
    }
    .adm-form input:focus, .adm-form select:focus, .adm-form textarea:focus { outline: none; border-color: var(--v600); box-shadow: 0 0 0 3px rgba(46, 125, 79, .2); }
    .adm-form .check { display: flex; gap: .5rem; align-items: center; margin-top: 1.2rem; }
    .adm-form .botones { display: flex; gap: .6rem; margin-top: 1.6rem; padding-top: 1.1rem; border-top: 1px solid var(--borde); }

    /* ---------- Panel de control ---------- */
    .dash-sub { margin: -.6rem 0 1.4rem; color: var(--muted); }
    .kpis { display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 1rem; margin-bottom: 1.2rem; }
    .kpi { position: relative; display: block; background: #fff; border: 1px solid var(--borde); border-radius: 12px; padding: 1.1rem 1.2rem 1rem; text-decoration: none; color: var(--texto); }
    .kpi::before { content: ''; position: absolute; top: 8px; left: 8px; width: 14px; height: 14px; border-top: 3px solid var(--ambar); border-left: 3px solid var(--ambar); border-radius: 4px 0 0 0; }
    a.kpi:hover { border-color: var(--v600); }
    .kpi-cab { display: flex; align-items: center; justify-content: space-between; margin-left: .9rem; color: var(--muted); font-size: .88rem; font-weight: 600; }
    .kpi-cab svg { width: 18px; height: 18px; fill: none; stroke: var(--v600); stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round; }
    .kpi-num { display: block; margin: .3rem 0 .1rem; font: 800 2.3rem/1.1 var(--titulo); letter-spacing: -.03em; }
    .kpi-pie { color: var(--muted); font-size: .85rem; }

    .fila { display: grid; grid-template-columns: 1.7fr 1fr; gap: 1rem; margin-bottom: 1rem; }
    .fila.iguales { grid-template-columns: 1fr 1fr; }
    .fila > * { min-width: 0; }
     /* Campos obligatorios */
    .adm-form label:has(+ :is(input, select, textarea)[required])::after { content: " *"; color: #C62828; font-weight: 700; }
    .adm-form form:has([required])::before { content: "Los campos marcados con * son obligatorios."; display: block; margin: .8rem 0 0; font-size: .78rem; color: var(--muted); }
    @media (max-width: 1000px) { .fila, .fila.iguales { grid-template-columns: 1fr; } }

    .panel .adm-tabla-wrap { border: 0; border-radius: 0; }
    .resumen { list-style: none; margin: 0; padding: 0; }
    .resumen li { display: flex; justify-content: space-between; align-items: baseline; gap: 1rem; padding: .75rem 1.1rem; border-bottom: 1px solid #E4EAE0; font-size: .95rem; }
    .resumen li:last-child { border-bottom: 0; }
    .resumen span { color: var(--muted); }
    .resumen strong { font-weight: 700; }
    .resumen strong.alerta-txt { color: var(--rojo); }

    .barras { list-style: none; margin: 0; padding: .5rem 1.1rem .7rem; }
    .barras li { display: grid; grid-template-columns: 34% 1fr 44px; gap: .8rem; align-items: center; padding: .5rem 0; font-size: .93rem; }
    .b-nom { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .b-pista { height: 10px; background: #E1E9DC; border-radius: 99px; overflow: hidden; }
    .b-pista i { display: block; height: 100%; background: var(--v600); border-radius: 99px; }
    .b-num { text-align: right; font-weight: 700; font-variant-numeric: tabular-nums; }
    .vacio-p { padding: 1.5rem 1.1rem; color: var(--muted); text-align: center; }

    .accesos { display: flex; flex-wrap: wrap; gap: .6rem; padding: 1.1rem; }
    .tag-stock { font-size: .75rem; font-weight: 700; padding: .08rem .5rem; border-radius: 99px; margin-left: .4rem; }
    .tag-stock.cero { background: #FBE9E7; color: #A3261E; }
    .tag-stock.bajo { background: #FFF1CC; color: #7A5200; }
</style>

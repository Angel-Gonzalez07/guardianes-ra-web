@extends('layouts.admin')

@section('contenido')
@include('admin._estilos')

@php $edicion = $orden->exists; @endphp

<div class="adm-form" style="max-width:920px">
    <h1 class="adm-titulo" style="margin-top:0">{{ $edicion ? 'Editar orden ' . $orden->folio : 'Nueva orden de compra' }}</h1>

    @if (! $proveedor)
        {{-- Paso 1: elegir proveedor --}}
        <form method="GET" action="{{ route('admin.compras.create') }}">
            <p style="margin:.6rem 0 0;color:var(--muted)">Los campos marcados con * son obligatorios.</p>

            <label for="proveedor">Proveedor</label>
            <select id="proveedor" name="proveedor" required>
                <option value="">— Elige un proveedor —</option>
                @foreach ($proveedores as $p)
                    <option value="{{ $p->id }}">{{ $p->nombre }}{{ $p->dias_entrega ? ' · entrega en ' . $p->dias_entrega . ' día(s)' : '' }}</option>
                @endforeach
            </select>

            <div class="botones">
                <button class="adm-btn" type="submit">Continuar</button>
                <a class="adm-btn sec" href="{{ route('admin.compras.index') }}">Cancelar</a>
            </div>
        </form>
    @else
        {{-- Paso 2: productos del proveedor, cantidades y costos --}}
        <form method="POST" action="{{ $edicion ? route('admin.compras.update', $orden) : route('admin.compras.store') }}">
            @csrf
            @if ($edicion)
                @method('PUT')
            @endif
            <input type="hidden" name="proveedor_id" value="{{ $proveedor->id }}">

            <p style="margin:.8rem 0 .2rem">
                Proveedor: <strong>{{ $proveedor->nombre }}</strong>
                @if ($proveedor->dias_entrega) · Entrega: {{ $proveedor->dias_entrega }} día(s) @endif
                @if ($proveedor->condiciones_pago) · Pago: {{ $proveedor->condiciones_pago }} @endif
            </p>
            <p style="margin:0 0 .8rem;color:var(--muted);font-size:.9rem">
                Escribe la cantidad y el costo unitario de lo que quieras pedir. Los productos con cantidad 0 no se incluyen.
                Campos con <span style="color:#C62828;font-weight:700">*</span>: costo obligatorio cuando la cantidad es mayor a 0.
            </p>

            @if ($productos->isEmpty())
                <div class="alerta err">Este proveedor no tiene productos activos. Asígnalo a un producto desde el catálogo de Productos.</div>
            @else
                <div class="adm-tabla-wrap">
                    <table class="adm-tabla">
                        <thead>
                            <tr><th>Producto</th><th>Stock actual</th><th style="width:130px">Cantidad</th><th style="width:170px">Costo unitario ($) <span style="color:#C62828">*</span></th></tr>
                        </thead>
                        <tbody>
                            @foreach ($productos as $p)
                                <tr>
                                    <td><strong>{{ $p->nombre }}</strong><br><small style="color:var(--muted)">{{ $p->sku }}</small></td>
                                    <td>{{ $p->stock }}</td>
                                    <td>
                                        <input type="number" name="cantidad[{{ $p->id }}]" min="0" max="9999" step="1"
                                               value="{{ old('cantidad.' . $p->id, $cantidades[$p->id] ?? 0) }}" aria-label="Cantidad de {{ $p->nombre }}"
                                               style="width:100%;padding:.5rem;border:1px solid var(--borde);border-radius:8px;font:inherit">
                                    </td>
                                    <td>
                                        <input type="number" name="costo[{{ $p->id }}]" min="0" max="99999.99" step="0.01" placeholder="0.00"
                                               value="{{ old('costo.' . $p->id, isset($costos[$p->id]) ? number_format((float) $costos[$p->id], 2, '.', '') : '') }}" aria-label="Costo unitario de {{ $p->nombre }}"
                                               style="width:100%;padding:.5rem;border:1px solid var(--borde);border-radius:8px;font:inherit">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <label for="notas">Notas (opcional)</label>
                <input type="text" id="notas" name="notas" maxlength="200" value="{{ old('notas', $orden->notas) }}">
            @endif

            <div class="botones">
                @if ($productos->isNotEmpty())
                    <button class="adm-btn" type="submit">{{ $edicion ? 'Guardar cambios' : 'Guardar como borrador' }}</button>
                @endif
                <a class="adm-btn sec" href="{{ $edicion ? route('admin.compras.show', $orden) : route('admin.compras.index') }}">Cancelar</a>
            </div>
        </form>
    @endif
</div>
@endsection

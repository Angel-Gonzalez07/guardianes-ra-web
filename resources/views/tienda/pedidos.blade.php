@extends('layouts.tienda')

@section('titulo', 'Mis pedidos')

@section('contenido')
    <h1 class="tit">Mis pedidos</h1>

    <div class="tarjeta tabla-wrap">
        <table>
            <thead><tr><th>Folio</th><th>Fecha</th><th>Total</th><th>Estado</th><th></th></tr></thead>
            <tbody>
                @forelse ($pedidos as $v)
                    <tr>
                        <td><strong>{{ $v->folio }}</strong></td>
                        <td>{{ $v->created_at->format('d/m/Y H:i') }}</td>
                        <td>${{ number_format($v->total, 2) }}</td>
                        <td><span class="etiqueta">{{ $v->estado }}</span></td>
                        <td><a class="btn btn-verde btn-sm" href="{{ route('tienda.pedido', $v) }}">Ver</a></td>
                    </tr>
                @empty
                    <tr><td colspan="5" style="text-align:center">Aún no tienes pedidos. <a href="{{ route('tienda') }}" style="color:var(--verde);font-weight:800">Ir a la tienda</a></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($pedidos->hasPages())
        <div style="display:flex;gap:.6rem;justify-content:center;margin-top:1rem;align-items:center">
            @if ($pedidos->previousPageUrl())<a class="btn btn-verde btn-sm" href="{{ $pedidos->previousPageUrl() }}">← Anterior</a>@endif
            <span>Página {{ $pedidos->currentPage() }} de {{ $pedidos->lastPage() }}</span>
            @if ($pedidos->nextPageUrl())<a class="btn btn-verde btn-sm" href="{{ $pedidos->nextPageUrl() }}">Siguiente →</a>@endif
        </div>
    @endif
@endsection
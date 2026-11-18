<div class="adm-tabla-wrap">
    <table class="adm-tabla">
        <thead><tr><th>Folio</th><th>Cliente</th><th>Total</th><th>Estado</th></tr></thead>
        <tbody>
            @forelse ($ultimasVentas as $v)
                <tr>
                    <td><a href="{{ route('admin.ventas.show', $v) }}"><strong>{{ $v->folio }}</strong></a></td>
                    <td>{{ $v->user->name ?? '—' }}</td>
                    <td>${{ number_format($v->total, 2) }}</td>
                    <td><span class="adm-badge" style="{{ $estadoColor[$v->estado] ?? '' }}">{{ $v->estado }}</span></td>
                </tr>
            @empty
                <tr><td colspan="4" class="adm-vacio">Todavía no hay pedidos.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
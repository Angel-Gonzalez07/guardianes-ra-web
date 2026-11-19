<aside class="tarjeta resumen-c">
    <h2>Tu pedido</h2>
    @foreach ($items as $i)
        <div class="linea"><span>{{ $i['cantidad'] }} x {{ $i['producto']->nombre }}</span><span>${{ number_format($i['subtotal'], 2) }}</span></div>
    @endforeach
    <div class="linea"><span>Subtotal</span><span>${{ number_format($subtotal, 2) }}</span></div>
    <div class="linea"><span>Envío</span><span>{{ $envio > 0 ? '$' . number_format($envio, 2) : 'Gratis' }}</span></div>
    <div class="linea total"><span>Total</span><span>${{ number_format($total, 2) }}</span></div>
</aside>
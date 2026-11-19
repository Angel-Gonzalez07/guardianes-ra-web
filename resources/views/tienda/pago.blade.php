@extends('layouts.tienda')

@section('titulo', 'Método de pago')

@section('contenido')
    <h1 class="tit">Método de pago</h1>
    <p class="sub">Elige cómo vas a pagar. El pago se confirma con el personal de la tienda.</p>

    @include('tienda._pasos', ['actual' => 3])

    <div class="compra">
        <form class="tarjeta" method="POST" action="{{ route('tienda.pago.guardar') }}">
            @csrf

            <fieldset style="border:0">
                <legend style="font-weight:800;margin-bottom:.4rem">Método de pago *</legend>

                <label style="display:flex;gap:.7rem;align-items:flex-start;font-weight:600;margin:.4rem 0;padding:.8rem;border:2px solid var(--menta);border-radius:14px;cursor:pointer">
                    <input type="radio" name="metodo_pago" value="Transferencia" style="width:auto;margin-top:.3rem" checked>
                    <span><strong>Transferencia</strong><br>
                        Al terminar tu pedido verás los datos de la cuenta. Haz la transferencia con tu folio como concepto.</span>
                </label>
            </fieldset>

            <div class="acciones-c">
                <button class="btn" type="submit">Revisar mi pedido</button>
                <a class="btn btn-verde" href="{{ route('tienda.envio') }}">Volver al envío</a>
            </div>
        </form>

        @include('tienda._resumen')
    </div>
@endsection
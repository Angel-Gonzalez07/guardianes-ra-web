@extends('layouts.tienda')

@section('titulo', 'Datos de envío')

@section('contenido')
    <h1 class="tit">Datos de envío</h1>
    <p class="sub">Indica a dónde enviaremos tu pedido. Los campos con * son obligatorios.</p>

    @include('tienda._pasos', ['actual' => 2])

    <div class="compra">
        <form class="tarjeta" method="POST" action="{{ route('tienda.envio.guardar') }}">
            @csrf

            <label for="destinatario">Nombre de quien recibe</label>
            <input type="text" id="destinatario" name="destinatario" value="{{ old('destinatario', $dato['destinatario'] ?? '') }}" required minlength="3" maxlength="100" autocomplete="name">

            <div class="grid2">
                <div>
                    <label for="telefono">Teléfono</label>
                    <input type="text" id="telefono" name="telefono" value="{{ old('telefono', $dato['telefono'] ?? '') }}" required inputmode="numeric" pattern="\d{10}" maxlength="10" autocomplete="tel-national">
                    <span class="campo-ayuda">10 dígitos, sin espacios ni guiones.</span>
                </div>
                <div>
                    <label for="codigo_postal">Código postal</label>
                    <input type="text" id="codigo_postal" name="codigo_postal" value="{{ old('codigo_postal', $dato['codigo_postal'] ?? '') }}" required inputmode="numeric" pattern="\d{5}" maxlength="5" autocomplete="postal-code">
                </div>
            </div>

            <label for="calle_numero">Calle y número</label>
            <input type="text" id="calle_numero" name="calle_numero" value="{{ old('calle_numero', $dato['calle_numero'] ?? '') }}" required minlength="5" maxlength="120" autocomplete="address-line1">

            <label for="colonia">Colonia</label>
            <input type="text" id="colonia" name="colonia" value="{{ old('colonia', $dato['colonia'] ?? '') }}" required minlength="3" maxlength="80">

            <div class="grid2">
                <div>
                    <label for="ciudad">Ciudad o municipio</label>
                    <input type="text" id="ciudad" name="ciudad" value="{{ old('ciudad', $dato['ciudad'] ?? '') }}" required minlength="2" maxlength="80" autocomplete="address-level2">
                </div>
                <div>
                    <label for="estado_republica">Estado</label>
                    <select id="estado_republica" name="estado_republica" required>
                        <option value="">Elige un estado</option>
                        @foreach ($estados as $e)
                            <option value="{{ $e }}" @selected(old('estado_republica', $dato['estado_republica'] ?? '') === $e)>{{ $e }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <label for="referencias">Referencias (opcional)</label>
            <textarea id="referencias" name="referencias" rows="2" maxlength="200">{{ old('referencias', $dato['referencias'] ?? '') }}</textarea>
            <span class="campo-ayuda">Por ejemplo: entre qué calles está, color de la casa o un punto conocido.</span>

            <div class="acciones-c">
                <button class="btn" type="submit">Continuar con el pago</button>
                <a class="btn btn-verde" href="{{ route('tienda.carrito') }}">Volver al carrito</a>
            </div>
        </form>

        @include('tienda._resumen')
    </div>
@endsection
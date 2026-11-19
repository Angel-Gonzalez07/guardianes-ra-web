@extends('layouts.admin')

@section('contenido')
@include('admin._estilos')

<div class="adm-form">
    <h1 class="adm-titulo" style="margin-top:0">{{ $proveedor->exists ? 'Editar proveedor' : 'Nuevo proveedor' }}</h1>

    <form method="POST"
          action="{{ $proveedor->exists ? route('admin.proveedores.update', $proveedor) : route('admin.proveedores.store') }}">
        @csrf
        @if ($proveedor->exists)
            @method('PUT')
        @endif

        <h2 style="font-size:1rem;margin:1.2rem 0 0;color:var(--v700)">Datos generales</h2>

        <label for="nombre">Nombre de la empresa</label>
        <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $proveedor->nombre) }}"
               required minlength="3" maxlength="120">

        <label for="rfc">RFC</label>
        <input type="text" id="rfc" name="rfc" value="{{ old('rfc', $proveedor->rfc) }}"
               required minlength="12" maxlength="13" placeholder="ABC010203XY1" style="text-transform:uppercase"
               pattern="[A-Za-zÑñ&amp;]{3,4}[0-9]{6}[A-Za-z0-9]{3}"
               title="12 caracteres (empresa) o 13 (persona física): 3 o 4 letras, 6 dígitos de fecha y 3 de homoclave">

        <label for="contacto">Persona de contacto</label>
        <input type="text" id="contacto" name="contacto" value="{{ old('contacto', $proveedor->contacto) }}"
               required minlength="3" maxlength="100">

        <label for="email">Correo</label>
        <input type="email" id="email" name="email" value="{{ old('email', $proveedor->email) }}"
               required maxlength="150" placeholder="ventas@empresa.com">

        <label for="telefono">Teléfono (10 dígitos)</label>
        <input type="text" id="telefono" name="telefono" value="{{ old('telefono', $proveedor->telefono) }}"
               required inputmode="numeric" pattern="[0-9]{10}" minlength="10" maxlength="10"
               title="Exactamente 10 dígitos, sin espacios ni guiones">

        <label for="direccion">Dirección (opcional)</label>
        <input type="text" id="direccion" name="direccion" value="{{ old('direccion', $proveedor->direccion) }}"
               maxlength="200">

        <h2 style="font-size:1rem;margin:1.6rem 0 0;color:var(--v700)">Datos comerciales</h2>

        <label for="ciudad">Ciudad</label>
        <input type="text" id="ciudad" name="ciudad" value="{{ old('ciudad', $proveedor->ciudad) }}"
               required minlength="2" maxlength="80">

        <label for="estado_republica">Estado</label>
        <select id="estado_republica" name="estado_republica" required>
            <option value="">— Elige el estado —</option>
            @foreach ($estados as $e)
                <option value="{{ $e }}" @selected(old('estado_republica', $proveedor->estado_republica) === $e)>{{ $e }}</option>
            @endforeach
        </select>

        <label for="dias_entrega">Días de entrega (1 a 90)</label>
        <input type="number" id="dias_entrega" name="dias_entrega" value="{{ old('dias_entrega', $proveedor->dias_entrega) }}"
               required min="1" max="90" step="1">

        <label for="condiciones_pago">Condiciones de pago</label>
        <select id="condiciones_pago" name="condiciones_pago" required>
            <option value="">— Elige una opción —</option>
            @foreach ($pagos as $c)
                <option value="{{ $c }}" @selected(old('condiciones_pago', $proveedor->condiciones_pago) === $c)>{{ $c }}</option>
            @endforeach
        </select>

        <label for="notas">Notas (opcional)</label>
        <textarea id="notas" name="notas" rows="3" maxlength="500">{{ old('notas', $proveedor->notas) }}</textarea>

        <label class="check">
            <input type="hidden" name="activo" value="0">
            <input type="checkbox" name="activo" value="1" @checked(old('activo', $proveedor->activo))>
            Proveedor activo
        </label>

        <div class="botones">
            <button class="adm-btn" type="submit">Guardar</button>
            <a class="adm-btn sec" href="{{ route('admin.proveedores.index') }}">Cancelar</a>
        </div>
    </form>
</div>
@endsection

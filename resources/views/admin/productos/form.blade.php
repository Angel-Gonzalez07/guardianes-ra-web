@extends('layouts.admin')

@section('contenido')
@include('admin._estilos')

<div class="adm-form">
    <h1 class="adm-titulo" style="margin-top:0">{{ $producto->exists ? 'Editar producto' : 'Nuevo producto' }}</h1>

    <form method="POST" enctype="multipart/form-data"
          action="{{ $producto->exists ? route('admin.productos.update', $producto) : route('admin.productos.store') }}">
        @csrf
        @if ($producto->exists)
            @method('PUT')
        @endif

        <label for="nombre">Nombre</label>
        <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $producto->nombre) }}"
               required minlength="3" maxlength="120">

        <label for="sku">SKU (código único)</label>
        <input type="text" id="sku" name="sku" value="{{ old('sku', $producto->sku) }}"
               required maxlength="30" pattern="[A-Za-z0-9\-]+" placeholder="GOTI-001"
               title="Solo letras, números y guiones" style="text-transform:uppercase">

        <label for="categoria_id">Categoría</label>
        <select id="categoria_id" name="categoria_id" required>
            <option value="">— Elige una categoría —</option>
            @foreach ($categorias as $c)
                <option value="{{ $c->id }}" @selected(old('categoria_id', $producto->categoria_id) == $c->id)>
                    {{ $c->nombre }}{{ $c->activo ? '' : ' (inactiva)' }}
                </option>
            @endforeach
        </select>

        <label for="proveedor_id">Proveedor</label>
        <select id="proveedor_id" name="proveedor_id" required>
            <option value="">— Elige un proveedor —</option>
            @foreach ($proveedores as $p)
                <option value="{{ $p->id }}" @selected(old('proveedor_id', $producto->proveedor_id) == $p->id)>
                    {{ $p->nombre }}{{ $p->activo ? '' : ' (inactivo)' }}
                </option>
            @endforeach
        </select>

        <label for="guardian">Guardián (opcional)</label>
        <select id="guardian" name="guardian">
            <option value="">— Ninguno —</option>
            @foreach ($guardianes as $g)
                <option value="{{ $g }}" @selected(old('guardian', $producto->guardian) === $g)>{{ $g }}</option>
            @endforeach
        </select>

        <label for="descripcion">Descripción (opcional)</label>
        <textarea id="descripcion" name="descripcion" rows="3" maxlength="1000">{{ old('descripcion', $producto->descripcion) }}</textarea>

        <label for="precio">Precio (MXN)</label>
        <input type="number" id="precio" name="precio" value="{{ old('precio', $producto->precio) }}"
               required step="0.01" min="0.01" max="99999999.99">

        <label for="stock">Stock</label>
        <input type="number" id="stock" name="stock" value="{{ old('stock', $producto->stock) }}"
               required step="1" min="0" max="100000">

        <label for="imagen">Imagen (jpg, png o webp, máx. 2 MB)</label>
        @if ($producto->imagen)
            <img src="{{ asset($producto->imagen) }}" alt="Imagen actual"
                 style="width:90px;height:90px;object-fit:cover;border-radius:10px;display:block;margin-bottom:.4rem">
        @endif
        <input type="file" id="imagen" name="imagen" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">

        <label class="check">
            <input type="hidden" name="activo" value="0">
            <input type="checkbox" name="activo" value="1" @checked(old('activo', $producto->activo))>
            Producto activo
        </label>

        <div class="botones">
            <button class="adm-btn" type="submit">Guardar</button>
            <a class="adm-btn sec" href="{{ route('admin.productos.index') }}">Cancelar</a>
        </div>
    </form>
</div>
@endsection

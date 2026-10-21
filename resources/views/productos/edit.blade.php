@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1>Editar producto</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('productos.update', $producto) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input
                id="nombre"
                name="nombre"
                type="text"
                class="form-control"
                maxlength="150"
                value="{{ old('nombre', $producto->nombre) }}"
                required
            >
        </div>

        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea
                id="descripcion"
                name="descripcion"
                class="form-control"
                rows="4"
                maxlength="2000"
                required
            >{{ old('descripcion', $producto->descripcion) }}</textarea>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="precio" class="form-label">Precio</label>
                <input
                    id="precio"
                    name="precio"
                    type="number"
                    class="form-control"
                    step="0.01"
                    min="0.01"
                    max="99999999.99"
                    value="{{ old('precio', $producto->precio) }}"
                    required
                >
            </div>

            <div class="col-md-6 mb-3">
                <label for="existencia" class="form-label">Existencias</label>
                <input
                    id="existencia"
                    name="existencia"
                    type="number"
                    class="form-control"
                    min="0"
                    max="2147483647"
                    value="{{ old('existencia', $producto->existencia) }}"
                    required
                >
            </div>
        </div>

        <div class="mb-3">
            <label for="categorias" class="form-label">Categorías</label>
            <select
                id="categorias"
                name="categorias[]"
                class="form-select"
                multiple
            >
                @foreach ($categorias as $categoria)
                    <option
                        value="{{ $categoria->id }}"
                        @selected(in_array(
                            $categoria->id,
                            old(
                                'categorias',
                                $producto->categorias->pluck('id')->all()
                            )
                        ))
                    >
                        {{ $categoria->nombre }}
                    </option>
                @endforeach
            </select>
            <small class="text-muted">
                Puedes seleccionar varias categorías.
            </small>
        </div>

        <button type="submit" class="btn btn-primary">
            Guardar cambios
        </button>

        <a href="{{ route('productos.index') }}" class="btn btn-secondary">
            Cancelar
        </a>
    </form>
</div>
@endsection

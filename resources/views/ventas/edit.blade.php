@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">Editar venta #{{ $venta->id }}</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('ventas.update', $venta) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="producto_id" class="form-label">Producto</label>
                    <select name="producto_id" id="producto_id" class="form-select">
                        @foreach ($productos as $producto)
                            <option value="{{ $producto->id }}"
                                {{ old('producto_id', $venta->producto_id) == $producto->id ? 'selected' : '' }}>
                                {{ $producto->nombre }}
                                — ${{ number_format((float) $producto->precio, 2) }}
                                (Existencia: {{ $producto->existencia }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="fecha" class="form-label">Fecha de venta</label>
                    <input type="date" name="fecha" id="fecha" class="form-control"
                           value="{{ old('fecha', $venta->fecha?->format('Y-m-d')) }}">
                </div>

                <button type="submit" class="btn btn-primary">Guardar cambios</button>
                <a href="{{ route('ventas.show', $venta) }}" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>
@endsection

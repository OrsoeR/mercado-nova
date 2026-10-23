@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="mb-2">Registrar venta</h1>
    <p class="text-muted mb-4">Selecciona el producto que se venderá.</p>

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
            <form action="{{ route('ventas.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="producto_id" class="form-label">Producto</label>
                    <select name="producto_id" id="producto_id" class="form-select" required>
                        <option value="">Selecciona un producto</option>
                        @foreach ($productos as $producto)
                            <option value="{{ $producto->id }}"
                                {{ old('producto_id') == $producto->id ? 'selected' : '' }}>
                                {{ $producto->nombre }}
                                — ${{ number_format((float) $producto->precio, 2) }}
                                (Existencia: {{ $producto->existencia }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="fecha" class="form-label">Fecha de venta</label>
                    <input type="date" name="fecha" id="fecha"
                           class="form-control" value="{{ old('fecha', now()->format('Y-m-d')) }}"
                           required>
                </div>

                <div class="alert alert-info">
                    El sistema asignará el cliente, el vendedor y el total automáticamente.
                </div>

                <button type="submit" class="btn btn-primary">Registrar venta</button>
                <a href="{{ route('ventas.index') }}" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>
@endsection

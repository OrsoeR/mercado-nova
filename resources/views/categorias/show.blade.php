@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Detalle de categoría</h1>
        <a href="{{ route('categorias.index') }}" class="btn btn-secondary">
            Volver
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <h2>{{ $categoria->nombre }}</h2>

            <p>{{ $categoria->descripcion ?? 'Sin descripción registrada.' }}</p>

            <hr>

            <h4>Productos asociados</h4>

            @forelse ($categoria->productos as $producto)
                <div class="d-flex justify-content-between border-bottom py-2">
                    <div>
                        <strong>{{ $producto->nombre }}</strong>
                        <div class="text-muted small">
                            Existencias: {{ $producto->existencia }}
                        </div>
                    </div>
                    <span>${{ number_format((float) $producto->precio, 2) }}</span>
                </div>
            @empty
                <p class="text-muted">Esta categoría todavía no tiene productos.</p>
            @endforelse

            @can('update', $categoria)
                <a href="{{ route('categorias.edit', $categoria) }}"
                   class="btn btn-primary mt-3">Editar categoría</a>
            @endcan
        </div>
    </div>
</div>
@endsection

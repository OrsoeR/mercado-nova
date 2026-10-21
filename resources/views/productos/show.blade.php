@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Detalle del producto</h1>
        <a href="{{ route('productos.index') }}" class="btn btn-secondary">
            Volver al catálogo
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <h2>{{ $producto->nombre }}</h2>

            <p class="text-muted">{{ $producto->descripcion }}</p>

            <h3 class="text-primary">
                ${{ number_format((float) $producto->precio, 2) }}
            </h3>

            <p>
                <strong>Existencias:</strong>
                {{ $producto->existencia }}
            </p>

            <p>
                <strong>Vendedor:</strong>
                {{ $producto->usuario?->name ?? 'Sin vendedor' }}
            </p>

            <p><strong>Categorías:</strong></p>
            @forelse ($producto->categorias as $categoria)
                <span class="badge bg-secondary">{{ $categoria->nombre }}</span>
            @empty
                <span class="text-muted">Sin categorías asignadas.</span>
            @endforelse

            @can('update', $producto)
                <div class="mt-4">
                    <a href="{{ route('productos.edit', $producto) }}"
                       class="btn btn-primary">Editar producto</a>
                </div>
            @endcan
        </div>
    </div>
</div>
@endsection

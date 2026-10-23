@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1>Catálogo de productos</h1>
            <p class="text-muted">Administra los productos de Mercado Nova.</p>
        </div>

        @can('create', App\Models\Producto::class)
            <a href="{{ route('productos.create') }}" class="btn btn-primary">
                + Nuevo producto
            </a>
        @endcan
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Precio</th>
                    <th>Existencias</th>
                    <th>Vendedor</th>
                    <th>Categorías</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($productos as $producto)
                    <tr>
                        <td>
                            <strong>{{ $producto->nombre }}</strong>
                            <div class="small text-muted">
                                {{ \Illuminate\Support\Str::limit($producto->descripcion, 65) }}
                            </div>
                        </td>
                        <td>${{ number_format((float) $producto->precio, 2) }}</td>
                        <td>{{ $producto->existencia }}</td>
                        <td>{{ $producto->usuario?->name ?? 'Sin vendedor' }}</td>
                        <td>
                            @forelse ($producto->categorias as $categoria)
                                <span class="badge bg-secondary">{{ $categoria->nombre }}</span>
                            @empty
                                <span class="text-muted">Sin categoría</span>
                            @endforelse
                        </td>
                        <td class="text-nowrap">
                            <a href="{{ route('productos.show', $producto) }}"
                               class="btn btn-sm btn-outline-info">Ver</a>

                            @can('update', $producto)
                                <a href="{{ route('productos.edit', $producto) }}"
                                   class="btn btn-sm btn-outline-primary">Editar</a>
                            @endcan

                            @can('delete', $producto)
                                <form action="{{ route('productos.destroy', $producto) }}"
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('¿Eliminar este producto?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        Eliminar
                                    </button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            No hay productos registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $productos->links() }}
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1>Categorías</h1>
            <p class="text-muted">Organiza los productos de Mercado Nova.</p>
        </div>

        @can('create', App\Models\Categoria::class)
            <a href="{{ route('categorias.create') }}" class="btn btn-primary">
                + Nueva categoría
            </a>
        @endcan
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Productos asociados</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($categorias as $categoria)
                    <tr>
                        <td><strong>{{ $categoria->nombre }}</strong></td>
                        <td>{{ \Illuminate\Support\Str::limit($categoria->descripcion ?? 'Sin descripción', 80) }}</td>
                        <td>{{ $categoria->productos_count ?? $categoria->productos->count() }}</td>
                        <td class="text-nowrap">
                            <a href="{{ route('categorias.show', $categoria) }}"
                               class="btn btn-sm btn-outline-info">Ver</a>

                            @can('update', $categoria)
                                <a href="{{ route('categorias.edit', $categoria) }}"
                                   class="btn btn-sm btn-outline-primary">Editar</a>
                            @endcan

                            @can('delete', $categoria)
                                <form action="{{ route('categorias.destroy', $categoria) }}"
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('¿Eliminar esta categoría?')">
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
                        <td colspan="4" class="text-center py-4">
                            No hay categorías registradas.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $categorias->links() }}
</div>
@endsection

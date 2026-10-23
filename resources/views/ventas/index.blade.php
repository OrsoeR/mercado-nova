@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1>Ventas</h1>
            <p class="text-muted">Consulta y administra las ventas registradas.</p>
        </div>

        @can('create', App\Models\Venta::class)
            <a href="{{ route('ventas.create') }}" class="btn btn-primary">
                Registrar venta
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

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Producto</th>
                            <th>Cliente</th>
                            <th>Vendedor</th>
                            <th>Fecha</th>
                            <th>Total</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($ventas as $venta)
                            <tr>
                                <td>{{ $venta->id }}</td>
                                <td>{{ $venta->producto->nombre ?? 'Producto no disponible' }}</td>
                                <td>{{ $venta->cliente->name ?? 'Cliente no disponible' }}</td>
                                <td>{{ $venta->vendedor->name ?? 'Vendedor no disponible' }}</td>
                                <td>{{ $venta->fecha?->format('d/m/Y') }}</td>
                                <td>${{ number_format((float) $venta->total, 2) }}</td>
                                <td>
                                    <a href="{{ route('ventas.show', $venta) }}"
                                       class="btn btn-sm btn-outline-info">
                                        Ver
                                    </a>

                                    @can('update', $venta)
                                        <a href="{{ route('ventas.edit', $venta) }}"
                                           class="btn btn-sm btn-outline-warning">
                                            Editar
                                        </a>
                                    @endcan

                                    @can('delete', $venta)
                                        <form action="{{ route('ventas.destroy', $venta) }}"
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('¿Deseas eliminar esta venta?')">
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
                                <td colspan="7" class="text-center text-muted py-4">
                                    Todavía no hay ventas registradas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $ventas->links() }}
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1>Detalle de venta #{{ $venta->id }}</h1>
            <p class="text-muted">Información del registro de venta.</p>
        </div>

        <a href="{{ route('ventas.index') }}" class="btn btn-secondary">
            Volver a ventas
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">Producto</dt>
                <dd class="col-sm-9">{{ $venta->producto->nombre ?? 'No disponible' }}</dd>

                <dt class="col-sm-3">Cliente</dt>
                <dd class="col-sm-9">{{ $venta->cliente->name ?? 'No disponible' }}</dd>

                <dt class="col-sm-3">Vendedor</dt>
                <dd class="col-sm-9">{{ $venta->vendedor->name ?? 'No disponible' }}</dd>

                <dt class="col-sm-3">Fecha</dt>
                <dd class="col-sm-9">{{ $venta->fecha?->format('d/m/Y') }}</dd>

                <dt class="col-sm-3">Total</dt>
                <dd class="col-sm-9">
                    <strong>${{ number_format((float) $venta->total, 2) }}</strong>
                </dd>
            </dl>
        </div>
    </div>
</div>
@endsection
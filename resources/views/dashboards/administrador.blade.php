@extends('layouts.app')

@section('title', 'Panel de Administrador')

@section('content')
<div class="container py-4">

    {{-- Encabezado --}}
    <div class="mb-4">
        <h1 class="fw-bold">Panel de Administrador</h1>
        <p class="text-muted">
            Bienvenido a Mercado Nova. Desde aquí puedes consultar
            las estadísticas y acceder a los módulos del sistema.
        </p>
    </div>

    {{-- Estadísticas --}}
    <h4 class="mb-3">Resumen general</h4>

    <div class="row g-3 mb-5">

        <div class="col-md-6 col-lg-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h6 class="text-muted">Total de usuarios</h6>
                    <h2 class="fw-bold">{{ $totalUsuarios }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h6 class="text-muted">Clientes</h6>
                    <h2 class="fw-bold">{{ $totalClientes }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h6 class="text-muted">Administradores</h6>
                    <h2 class="fw-bold">{{ $totalAdministradores }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h6 class="text-muted">Empleados</h6>
                    <h2 class="fw-bold">{{ $totalEmpleados }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h6 class="text-muted">Gerentes</h6>
                    <h2 class="fw-bold">{{ $totalGerentes }}</h2>
                </div>
            </div>
        </div>

    </div>

    {{-- Accesos a los módulos --}}
    <h4 class="mb-3">Administración del sistema</h4>

    <div class="row g-3">

        {{-- Usuarios --}}
        <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">Usuarios</h5>
                    <p class="card-text text-muted">
                        Administra las cuentas y los roles.
                    </p>
                    <a href="{{ route('usuarios.index') }}"
                       class="btn btn-primary mt-auto">
                        Ver usuarios
                    </a>
                </div>
            </div>
        </div>

        {{-- Productos --}}
        <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">Productos</h5>
                    <p class="card-text text-muted">
                        Consulta el catálogo de productos.
                    </p>
                    <a href="{{ route('productos.index') }}"
                       class="btn btn-primary mt-auto">
                        Ver productos
                    </a>
                </div>
            </div>
        </div>

        {{-- Categorías --}}
        <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">Categorías</h5>
                    <p class="card-text text-muted">
                        Consulta las categorías registradas.
                    </p>
                    <a href="{{ route('categorias.index') }}"
                       class="btn btn-primary mt-auto">
                        Ver categorías
                    </a>
                </div>
            </div>
        </div>

        {{-- Ventas --}}
        <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">Ventas</h5>
                    <p class="card-text text-muted">
                        Consulta los registros de ventas.
                    </p>
                    <a href="{{ route('ventas.index') }}"
                       class="btn btn-primary mt-auto">
                        Ver ventas
                    </a>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection

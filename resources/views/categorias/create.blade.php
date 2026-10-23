@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1>Nueva categoría</h1>
    <p class="text-muted">Registra una categoría para clasificar productos.</p>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('categorias.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input id="nombre" name="nombre" type="text"
                   class="form-control" maxlength="100"
                   value="{{ old('nombre') }}" required>
        </div>

        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea id="descripcion" name="descripcion"
                      class="form-control" rows="4"
                      maxlength="2000">{{ old('descripcion') }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">Guardar categoría</button>
        <a href="{{ route('categorias.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection

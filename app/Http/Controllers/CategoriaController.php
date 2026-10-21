<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoriaRequest;
use App\Http\Requests\UpdateCategoriaRequest;
use App\Models\Categoria;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class CategoriaController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Categoria::class);

        $categorias = Categoria::withCount('productos')
            ->orderBy('nombre')
            ->paginate(10);

        return view('categorias.index', compact('categorias'));
    }

    public function create(): View
    {
        $this->authorize('create', Categoria::class);

        return view('categorias.create');
    }

    public function store(StoreCategoriaRequest $request): RedirectResponse
    {
        $categoria = Categoria::create($request->validated());

        Log::info('Categoría creada', [
            'categoria_id' => $categoria->id,
            'user_id' => auth()->id(),
            'ip' => $request->ip(),
        ]);

        return redirect()
            ->route('categorias.index')
            ->with('success', 'Categoría creada correctamente.');
    }

    public function show(Categoria $categoria): View
    {
        $this->authorize('view', $categoria);

        $categoria->load('productos');

        return view('categorias.show', compact('categoria'));
    }

    public function edit(Categoria $categoria): View
    {
        $this->authorize('update', $categoria);

        return view('categorias.edit', compact('categoria'));
    }

    public function update(
        UpdateCategoriaRequest $request,
        Categoria $categoria
    ): RedirectResponse {
        $categoria->update($request->validated());

        Log::info('Categoría actualizada', [
            'categoria_id' => $categoria->id,
            'user_id' => auth()->id(),
            'ip' => $request->ip(),
        ]);

        return redirect()
            ->route('categorias.index')
            ->with('success', 'Categoría actualizada correctamente.');
    }

    public function destroy(Categoria $categoria): RedirectResponse
    {
        $this->authorize('delete', $categoria);

        $categoriaId = $categoria->id;
        $categoria->delete();

        Log::info('Categoría eliminada', [
            'categoria_id' => $categoriaId,
            'user_id' => auth()->id(),
            'ip' => request()->ip(),
        ]);

        return redirect()
            ->route('categorias.index')
            ->with('success', 'Categoría eliminada correctamente.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductoRequest;
use App\Http\Requests\UpdateProductoRequest;
use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ProductoController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Producto::class);

        $productos = Producto::with(['usuario', 'categorias'])
            ->latest()
            ->paginate(10);

        return view('productos.index', compact('productos'));
    }

    public function create(): View
    {
        $this->authorize('create', Producto::class);

        $categorias = Categoria::orderBy('nombre')->get();

        return view('productos.create', compact('categorias'));
    }

    public function store(StoreProductoRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // El usuario autenticado queda asociado al producto.
        $data['usuario_id'] = $request->user()->id;

        $categorias = $data['categorias'] ?? [];
        unset($data['categorias']);

        $producto = Producto::create($data);

        if (!empty($categorias)) {
            $producto->categorias()->sync($categorias);
        }

        Log::channel('productos')->info('Producto creado', [
            'producto_id' => $producto->id,
            'usuario_id' => $request->user()->id,
            'ip' => $request->ip(),
        ]);

        return redirect()
            ->route('productos.index')
            ->with('success', 'Producto creado correctamente.');
    }

    public function show(Producto $producto): View
    {
        $this->authorize('view', $producto);

        $producto->load(['usuario', 'categorias']);

        return view('productos.show', compact('producto'));
    }

    public function edit(Producto $producto): View
    {
        $this->authorize('update', $producto);

        $categorias = Categoria::orderBy('nombre')->get();

        $producto->load('categorias');

        return view('productos.edit', compact('producto', 'categorias'));
    }

    public function update(
        UpdateProductoRequest $request,
        Producto $producto
    ): RedirectResponse {
        $data = $request->validated();

        // Guardar las categorías antes de retirarlas de los datos del producto.
        $categorias = $data['categorias'] ?? null;
        unset($data['categorias']);

        $producto->update($data);

        // Sincronizar categorías solo si se enviaron en la solicitud.
        if ($categorias !== null) {
            $producto->categorias()->sync($categorias);
        }

        Log::channel('productos')->info('Producto actualizado', [
            'producto_id' => $producto->id,
            'usuario_id' => $request->user()->id,
            'ip' => $request->ip(),
        ]);

        return redirect()
            ->route('productos.index')
            ->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(Producto $producto): RedirectResponse
    {
        $this->authorize('delete', $producto);

        $id = $producto->id;
        $usuarioId = request()->user()->id;

        $producto->delete();

        Log::channel('productos')->info('Producto eliminado', [
            'producto_id' => $id,
            'usuario_id' => $usuarioId,
            'ip' => request()->ip(),
        ]);

        return redirect()
            ->route('productos.index')
            ->with('success', 'Producto eliminado correctamente.');
    }
}

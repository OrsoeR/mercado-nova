<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVentaRequest;
use App\Http\Requests\UpdateVentaRequest;
use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class VentaController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Venta::class);

        $usuario = request()->user();

        $consulta = Venta::with([
            'producto',
            'cliente',
            'vendedor',
        ]);

        // El administrador puede consultar todas las ventas.
        // Los demás usuarios solo consultan las propias.
        if (! $usuario->hasRole('administrador')) {
            $consulta->where(function ($query) use ($usuario) {
                $query->where('cliente_id', $usuario->id)
                    ->orWhere('vendedor_id', $usuario->id);
            });
        }

        $ventas = $consulta->latest()->paginate(10);

        return view('ventas.index', compact('ventas'));
    }

    public function create(): View
    {
        $this->authorize('create', Venta::class);

        $productos = Producto::where('existencia', '>', 0)
            ->orderBy('nombre')
            ->get();

        return view('ventas.create', compact('productos'));
    }

    public function store(StoreVentaRequest $request): RedirectResponse
    {
        $datos = $request->validated();

        $venta = DB::transaction(function () use ($datos, $request) {
            $producto = Producto::whereKey($datos['producto_id'])
                ->lockForUpdate()
                ->firstOrFail();

            if ($producto->existencia < 1) {
                abort(422, 'El producto no tiene existencias.');
            }

            $producto->decrement('existencia', 1);

            return Venta::create([
                'producto_id' => $producto->id,
                'cliente_id' => $request->user()->id,
                'vendedor_id' => $producto->usuario_id,
                'fecha' => $datos['fecha'],
                'total' => $producto->precio,
            ]);
        });

        Log::channel('ventas')->info('Venta creada', [
            'venta_id' => $venta->id,
            'producto_id' => $venta->producto_id,
            'cliente_id' => $venta->cliente_id,
            'vendedor_id' => $venta->vendedor_id,
            'total' => $venta->total,
            'ip' => $request->ip(),
        ]);

        return redirect()
            ->route('ventas.index')
            ->with('success', 'Venta registrada correctamente.');
    }

    public function show(Venta $venta): View
    {
        $this->authorize('view', $venta);

        $venta->load(['producto', 'cliente', 'vendedor']);

        return view('ventas.show', compact('venta'));
    }

    public function edit(Venta $venta): View
    {
        $this->authorize('update', $venta);

        $productos = Producto::orderBy('nombre')->get();

        return view('ventas.edit', compact('venta', 'productos'));
    }

    public function update(
        UpdateVentaRequest $request,
        Venta $venta
    ): RedirectResponse {
        $datos = $request->validated();

        $ventaId = $venta->id;

        DB::transaction(function () use ($datos, $ventaId) {
            // Bloquear la venta para evitar actualizaciones simultáneas.
            $ventaActual = Venta::whereKey($ventaId)
                ->lockForUpdate()
                ->firstOrFail();

            $productoAnteriorId = $ventaActual->producto_id;
            $productoNuevoId = $datos['producto_id']
                ?? $productoAnteriorId;

            if ($productoNuevoId !== $productoAnteriorId) {
                // Bloquear los productos en orden de ID.
                $ids = collect([
                    $productoAnteriorId,
                    $productoNuevoId,
                ])->unique()->sort()->values();

                $productos = Producto::whereIn('id', $ids)
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                $productoAnterior = $productos->get(
                    $productoAnteriorId
                );

                $productoNuevo = $productos->get(
                    $productoNuevoId
                );

                if (! $productoAnterior || ! $productoNuevo) {
                    abort(422, 'No se encontraron los productos de la venta.');
                }

                if ($productoNuevo->existencia < 1) {
                    abort(422, 'El nuevo producto no tiene existencias.');
                }

                // Devolver una unidad al producto anterior.
                $productoAnterior->increment('existencia', 1);

                // Descontar una unidad del nuevo producto.
                $productoNuevo->decrement('existencia', 1);

                $ventaActual->producto_id = $productoNuevo->id;
                $ventaActual->total = $productoNuevo->precio;
            }

            if (isset($datos['fecha'])) {
                $ventaActual->fecha = $datos['fecha'];
            }

            $ventaActual->save();
        });

        Log::channel('ventas')->info('Venta actualizada', [
            'venta_id' => $ventaId,
            'user_id' => $request->user()->id,
            'ip' => $request->ip(),
        ]);

        return redirect()
            ->route('ventas.index')
            ->with('success', 'Venta actualizada correctamente.');
    }

    public function destroy(Venta $venta): RedirectResponse
    {
        $this->authorize('delete', $venta);

        $ventaId = $venta->id;

        DB::transaction(function () use ($ventaId) {
            $ventaActual = Venta::whereKey($ventaId)
                ->lockForUpdate()
                ->firstOrFail();

            $producto = Producto::whereKey(
                $ventaActual->producto_id
            )->lockForUpdate()->firstOrFail();

            // Restaurar el inventario antes de eliminar la venta.
            $producto->increment('existencia', 1);

            $ventaActual->delete();
        });

        Log::channel('ventas')->info('Venta eliminada', [
            'venta_id' => $ventaId,
            'user_id' => request()->user()->id,
            'ip' => request()->ip(),
        ]);

        return redirect()
            ->route('ventas.index')
            ->with('success', 'Venta eliminada y existencias restauradas.');
    }
}

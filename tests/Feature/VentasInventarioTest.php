<?php

namespace Tests\Feature;

use App\Models\Producto;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VentasInventarioTest extends TestCase
{
    use RefreshDatabase;

    public function test_registrar_venta_disminuye_la_existencia(): void
    {
        $cliente = User::factory()->create([
            'role' => 'cliente',
        ]);

        $vendedor = User::factory()->create([
            'role' => 'gerente',
        ]);

        $producto = Producto::create([
            'nombre' => 'Sombrero de prueba',
            'descripcion' => 'Producto creado para una prueba automatizada.',
            'precio' => 850.00,
            'existencia' => 5,
            'usuario_id' => $vendedor->id,
        ]);

        $this->actingAs($cliente)
            ->post(route('ventas.store'), [
                'producto_id' => $producto->id,
                'fecha' => now()->toDateString(),
            ])
            ->assertRedirect(route('ventas.index'))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('ventas', [
            'producto_id' => $producto->id,
            'cliente_id' => $cliente->id,
            'vendedor_id' => $vendedor->id,
            'total' => '850.00',
        ]);

        $this->assertSame(4, $producto->fresh()->existencia);
    }

    public function test_no_se_puede_vender_un_producto_sin_existencias(): void
    {
        $cliente = User::factory()->create([
            'role' => 'cliente',
        ]);

        $vendedor = User::factory()->create([
            'role' => 'gerente',
        ]);

        $producto = Producto::create([
            'nombre' => 'Producto agotado',
            'descripcion' => 'Producto sin existencias para la prueba.',
            'precio' => 250.00,
            'existencia' => 0,
            'usuario_id' => $vendedor->id,
        ]);

        $this->actingAs($cliente)
            ->post(route('ventas.store'), [
                'producto_id' => $producto->id,
                'fecha' => now()->toDateString(),
            ])
            ->assertUnprocessable();

        $this->assertDatabaseMissing('ventas', [
            'producto_id' => $producto->id,
            'cliente_id' => $cliente->id,
        ]);

        $this->assertSame(0, $producto->fresh()->existencia);
    }

    public function test_administrador_cambia_producto_y_actualiza_existencias(): void
    {
        $admin = User::factory()->create([
            'role' => 'administrador',
        ]);

        $cliente = User::factory()->create([
            'role' => 'cliente',
        ]);

        $vendedor = User::factory()->create([
            'role' => 'gerente',
        ]);

        $productoAnterior = Producto::create([
            'nombre' => 'Producto anterior',
            'descripcion' => 'Producto para probar la edición.',
            'precio' => 100.00,
            'existencia' => 3,
            'usuario_id' => $vendedor->id,
        ]);

        $productoNuevo = Producto::create([
            'nombre' => 'Producto nuevo',
            'descripcion' => 'Producto de reemplazo.',
            'precio' => 200.00,
            'existencia' => 5,
            'usuario_id' => $vendedor->id,
        ]);

        $venta = Venta::create([
            'producto_id' => $productoAnterior->id,
            'cliente_id' => $cliente->id,
            'vendedor_id' => $vendedor->id,
            'fecha' => now()->toDateString(),
            'total' => 100.00,
        ]);

        $this->actingAs($admin)
            ->put(route('ventas.update', $venta), [
                'producto_id' => $productoNuevo->id,
            ])
            ->assertRedirect(route('ventas.index'))
            ->assertSessionHasNoErrors();

        $this->assertSame(4, $productoAnterior->fresh()->existencia);
        $this->assertSame(4, $productoNuevo->fresh()->existencia);

        $this->assertDatabaseHas('ventas', [
            'id' => $venta->id,
            'producto_id' => $productoNuevo->id,
            'total' => '200.00',
        ]);
    }

    public function test_administrador_elimina_venta_y_restablece_existencia(): void
    {
        $admin = User::factory()->create([
            'role' => 'administrador',
        ]);

        $cliente = User::factory()->create([
            'role' => 'cliente',
        ]);

        $vendedor = User::factory()->create([
            'role' => 'gerente',
        ]);

        $producto = Producto::create([
            'nombre' => 'Producto vendido',
            'descripcion' => 'Producto para probar la eliminación.',
            'precio' => 350.00,
            'existencia' => 4,
            'usuario_id' => $vendedor->id,
        ]);

        $venta = Venta::create([
            'producto_id' => $producto->id,
            'cliente_id' => $cliente->id,
            'vendedor_id' => $vendedor->id,
            'fecha' => now()->toDateString(),
            'total' => 350.00,
        ]);

        $this->actingAs($admin)
            ->delete(route('ventas.destroy', $venta))
            ->assertRedirect(route('ventas.index'))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseMissing('ventas', [
            'id' => $venta->id,
        ]);

        $this->assertSame(5, $producto->fresh()->existencia);
    }

    public function test_gerente_no_puede_editar_una_venta(): void
    {
        $gerente = User::factory()->create([
            'role' => 'gerente',
        ]);

        $cliente = User::factory()->create([
            'role' => 'cliente',
        ]);

        $producto = Producto::create([
            'nombre' => 'Producto protegido',
            'descripcion' => 'Producto para probar permisos.',
            'precio' => 150.00,
            'existencia' => 3,
            'usuario_id' => $gerente->id,
        ]);

        $venta = Venta::create([
            'producto_id' => $producto->id,
            'cliente_id' => $cliente->id,
            'vendedor_id' => $gerente->id,
            'fecha' => now()->toDateString(),
            'total' => 150.00,
        ]);

        $this->actingAs($gerente)
            ->put(route('ventas.update', $venta), [
                'fecha' => now()->toDateString(),
            ])
            ->assertForbidden();

        $this->assertDatabaseHas('ventas', [
            'id' => $venta->id,
            'producto_id' => $producto->id,
        ]);

        $this->assertSame(3, $producto->fresh()->existencia);
    }

    public function test_gerente_no_puede_eliminar_una_venta(): void
    {
        $gerente = User::factory()->create([
            'role' => 'gerente',
        ]);

        $cliente = User::factory()->create([
            'role' => 'cliente',
        ]);

        $producto = Producto::create([
            'nombre' => 'Producto protegido',
            'descripcion' => 'Producto para probar permisos.',
            'precio' => 150.00,
            'existencia' => 3,
            'usuario_id' => $gerente->id,
        ]);

        $venta = Venta::create([
            'producto_id' => $producto->id,
            'cliente_id' => $cliente->id,
            'vendedor_id' => $gerente->id,
            'fecha' => now()->toDateString(),
            'total' => 150.00,
        ]);

        $this->actingAs($gerente)
            ->delete(route('ventas.destroy', $venta))
            ->assertForbidden();

        $this->assertDatabaseHas('ventas', [
            'id' => $venta->id,
        ]);

        $this->assertSame(3, $producto->fresh()->existencia);
    }
}

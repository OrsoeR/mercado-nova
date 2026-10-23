<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermisosCatalogoTest extends TestCase
{
    use RefreshDatabase;

    private function crearProducto(User $propietario): Producto
    {
        return Producto::create([
            'nombre' => 'Sombrero de prueba',
            'descripcion' => 'Producto para verificar permisos.',
            'precio' => 500.00,
            'existencia' => 10,
            'usuario_id' => $propietario->id,
        ]);
    }

    private function crearCategoria(): Categoria
    {
        return Categoria::create([
            'nombre' => 'Charros',
            'descripcion' => 'Categoría de prueba.',
        ]);
    }

    public function test_cliente_puede_consultar_productos(): void
    {
        $cliente = User::factory()->create([
            'role' => User::ROLE_CLIENTE,
        ]);

        $producto = $this->crearProducto(
            User::factory()->create([
                'role' => User::ROLE_GERENTE,
            ])
        );

        $this->actingAs($cliente)
            ->get(route('productos.index'))
            ->assertOk();

        $this->actingAs($cliente)
            ->get(route('productos.show', $producto))
            ->assertOk();
    }

    public function test_cliente_no_puede_crear_editar_ni_eliminar_productos(): void
    {
        $cliente = User::factory()->create([
            'role' => User::ROLE_CLIENTE,
        ]);

        $producto = $this->crearProducto(
            User::factory()->create([
                'role' => User::ROLE_GERENTE,
            ])
        );

        $this->actingAs($cliente)
            ->get(route('productos.create'))
            ->assertForbidden();

        $this->actingAs($cliente)
            ->put(route('productos.update', $producto), [
                'nombre' => 'Producto modificado',
            ])
            ->assertForbidden();

        $this->actingAs($cliente)
            ->delete(route('productos.destroy', $producto))
            ->assertForbidden();
    }

    public function test_gerente_no_puede_modificar_producto_de_otro_gerente(): void
    {
        $gerente = User::factory()->create([
            'role' => User::ROLE_GERENTE,
        ]);

        $otroGerente = User::factory()->create([
            'role' => User::ROLE_GERENTE,
        ]);

        $producto = $this->crearProducto($otroGerente);

        $this->actingAs($gerente)
            ->put(route('productos.update', $producto), [
                'nombre' => 'Producto ajeno modificado',
            ])
            ->assertForbidden();

        $this->actingAs($gerente)
            ->delete(route('productos.destroy', $producto))
            ->assertForbidden();
    }

    public function test_cliente_puede_consultar_categorias(): void
    {
        $cliente = User::factory()->create([
            'role' => User::ROLE_CLIENTE,
        ]);

        $categoria = $this->crearCategoria();

        $this->actingAs($cliente)
            ->get(route('categorias.index'))
            ->assertOk();

        $this->actingAs($cliente)
            ->get(route('categorias.show', $categoria))
            ->assertOk();
    }

    public function test_cliente_no_puede_crear_editar_ni_eliminar_categorias(): void
    {
        $cliente = User::factory()->create([
            'role' => User::ROLE_CLIENTE,
        ]);

        $categoria = $this->crearCategoria();

        $this->actingAs($cliente)
            ->get(route('categorias.create'))
            ->assertForbidden();

        $this->actingAs($cliente)
            ->put(route('categorias.update', $categoria), [
                'nombre' => 'Categoría modificada',
            ])
            ->assertForbidden();

        $this->actingAs($cliente)
            ->delete(route('categorias.destroy', $categoria))
            ->assertForbidden();
    }

    public function test_gerente_no_puede_editar_ni_eliminar_a_otro_gerente(): void
    {
        $gerente = User::factory()->create([
            'role' => User::ROLE_GERENTE,
        ]);

        $otroGerente = User::factory()->create([
            'role' => User::ROLE_GERENTE,
        ]);

        $this->actingAs($gerente)
            ->put(route('usuarios.update', $otroGerente), [
                'name' => 'Gerente modificado',
                'email' => $otroGerente->email,
                'role' => User::ROLE_GERENTE,
            ])
            ->assertForbidden();

        $this->actingAs($gerente)
            ->delete(route('usuarios.destroy', $otroGerente))
            ->assertForbidden();
    }

    public function test_gerente_no_puede_editar_ni_eliminar_administrador(): void
    {
        $gerente = User::factory()->create([
            'role' => User::ROLE_GERENTE,
        ]);

        $admin = User::factory()->create([
            'role' => User::ROLE_ADMINISTRADOR,
        ]);

        $this->actingAs($gerente)
            ->put(route('usuarios.update', $admin), [
                'name' => 'Administrador modificado',
                'email' => $admin->email,
                'role' => User::ROLE_ADMINISTRADOR,
            ])
            ->assertForbidden();

        $this->actingAs($gerente)
            ->delete(route('usuarios.destroy', $admin))
            ->assertForbidden();
    }

    public function test_administrador_puede_editar_usuario_cliente(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMINISTRADOR,
        ]);

        $cliente = User::factory()->create([
            'role' => User::ROLE_CLIENTE,
        ]);

        $this->actingAs($admin)
            ->put(route('usuarios.update', $cliente), [
                'name' => 'Cliente actualizado',
                'email' => $cliente->email,
                'role' => User::ROLE_CLIENTE,
            ])
            ->assertRedirect(route('usuarios.index'));

        $this->assertDatabaseHas('users', [
            'id' => $cliente->id,
            'name' => 'Cliente actualizado',
            'role' => User::ROLE_CLIENTE,
        ]);
    }

    public function test_administrador_no_puede_eliminarse_a_si_mismo(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMINISTRADOR,
        ]);

        $this->actingAs($admin)
            ->delete(route('usuarios.destroy', $admin))
            ->assertForbidden();

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
        ]);
    }

    public function test_gerente_no_puede_cambiar_su_propio_rol(): void
    {
        $gerente = User::factory()->create([
            'role' => User::ROLE_GERENTE,
        ]);

        $this->actingAs($gerente)
            ->put(route('usuarios.update', $gerente), [
                'name' => $gerente->name,
                'email' => $gerente->email,
                'role' => User::ROLE_ADMINISTRADOR,
            ])
            ->assertForbidden();

        $this->assertDatabaseHas('users', [
            'id' => $gerente->id,
            'role' => User::ROLE_GERENTE,
        ]);
    }
}

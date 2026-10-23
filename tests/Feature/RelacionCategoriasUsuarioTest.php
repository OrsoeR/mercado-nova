<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RelacionCategoriasUsuarioTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_obtiene_solo_categorias_de_sus_productos(): void
    {
        $usuario = User::factory()->create();
        $otroUsuario = User::factory()->create();

        $categoriaPropia = Categoria::create([
            'nombre' => 'Sombreros',
            'descripcion' => 'Sombreros de prueba.',
        ]);

        $categoriaAjena = Categoria::create([
            'nombre' => 'Botas',
            'descripcion' => 'Botas de prueba.',
        ]);

        $productoPropio = Producto::create([
            'nombre' => 'Sombrero charro',
            'descripcion' => 'Producto del primer usuario.',
            'precio' => 500,
            'existencia' => 10,
            'usuario_id' => $usuario->id,
        ]);

        $productoAjeno = Producto::create([
            'nombre' => 'Botas charras',
            'descripcion' => 'Producto del segundo usuario.',
            'precio' => 900,
            'existencia' => 5,
            'usuario_id' => $otroUsuario->id,
        ]);

        $productoPropio->categorias()->attach($categoriaPropia->id);
        $productoAjeno->categorias()->attach($categoriaAjena->id);


        $categorias = $usuario->categoriasDeProductos()
            ->pluck('categorias.id')
            ->all();

        $this->assertCount(1, $categorias);
        $this->assertSame(
            $categoriaPropia->id,
            $categorias[0]
        );

        $this->assertCount(
            1,
            $usuario->asociacionesCategorias
        );

        $this->assertSame(
            $categoriaPropia->id,
            $usuario->asociacionesCategorias->first()->categoria_id
        );
    }
}

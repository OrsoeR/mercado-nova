<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Database\Seeder;

class CatalogoSeeder extends Seeder
{
    public function run(): void
    {
        // Buscar al administrador de demostración.
        $administrador = User::where(
            'email',
            'admin@mercadonova.mx'
        )->first();

        // Si todavía no existe, no continuar.
        if (!$administrador) {
            $this->command?->warn(
                'No existe admin@mercadonova.mx. Ejecuta primero DatabaseSeeder.'
            );

            return;
        }

        // Crear o recuperar una categoría de demostración.
        $categoria = Categoria::firstOrCreate(
            ['nombre' => 'Accesorios'],
            ['descripcion' => 'Accesorios para el catálogo de Mercado Nova.']
        );

        // Crear o recuperar un producto de demostración.
        $producto = Producto::firstOrCreate(
            [
                'nombre' => 'Producto de demostración',
                'usuario_id' => $administrador->id,
            ],
            [
                'descripcion' => 'Producto de ejemplo para probar el catálogo.',
                'precio' => 150.00,
                'existencia' => 10,
            ]
        );

        // Asociar el producto con la categoría sin duplicar la relación.
        $producto->categorias()->syncWithoutDetaching([
            $categoria->id,
        ]);

        $this->command?->info(
            'Catálogo de demostración preparado correctamente.'
        );
    }
}

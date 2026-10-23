<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Usuario;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Crea las cuentas de demostración y los datos iniciales.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@mercadonova.mx'],
            [
                'name' => 'Admin Mercado Nova',
                'role' => User::ROLE_ADMINISTRADOR,
                'phone' => '9611000000',
                'password' => 'Password123',
            ]
        );

        User::updateOrCreate(
            ['email' => 'gerente@mercadonova.mx'],
            [
                'name' => 'Laura Gerente',
                'role' => User::ROLE_GERENTE,
                'phone' => '9611000001',
                'password' => 'Password123',
            ]
        );

        User::updateOrCreate(
            ['email' => 'empleado@mercadonova.mx'],
            [
                'name' => 'Carlos Empleado',
                'role' => User::ROLE_EMPLEADO,
                'phone' => '9611000002',
                'password' => 'Password123',
            ]
        );

        User::updateOrCreate(
            ['email' => 'cliente@mercadonova.mx'],
            [
                'name' => 'Ana Cliente',
                'role' => User::ROLE_CLIENTE,
                'phone' => '9611000003',
                'password' => 'Password123',
            ]
        );

        // Generar usuarios ficticios solo si aún no existen.
        $usuariosDeFabrica = User::where(
            'email',
            'like',
            '%@tuxtla.tecnm.mx'
        )->count();

        if ($usuariosDeFabrica === 0) {
            Usuario::factory(12)->create();
        }

        $this->call(CatalogoSeeder::class);
    }
}

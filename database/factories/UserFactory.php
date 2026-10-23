<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected static ?string $password = null;

    public function definition(): array
    {
        $nombres = ['Juan', 'Mario', 'Maria', 'Pedro'];

        $apellidos = [
            'Lopez',
            'Sanchez',
            'Hernandez',
            'Martinez',
        ];

        $nombre = fake()->randomElement($nombres);
        $apellido = fake()->randomElement($apellidos);

        $correoBase = strtolower(
            substr($nombre, 0, 1) . $apellido
        );

        $correo = $correoBase . '@tuxtla.tecnm.mx';
        $sufijo = 1;

        while (User::where('email', $correo)->exists()) {
            $correo = $correoBase . $sufijo
                . '@tuxtla.tecnm.mx';

            $sufijo++;
        }

        return [
            'name' => $nombre . ' ' . $apellido,
            'apellidos' => $apellido,
            'email' => $correo,
            'role' => fake()->randomElement([
                User::ROLE_CLIENTE,
                User::ROLE_GERENTE,
            ]),
            'phone' => fake()->numerify('961#######'),
            'email_verified_at' => now(),
            'password' => static::$password
                ??= Hash::make('123'),
            'remember_token' => Str::random(10),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}

<?php

namespace App\Policies;

use App\Models\Producto;
use App\Models\User;

class ProductoPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(
            User::ROLE_CLIENTE,
            User::ROLE_EMPLEADO,
            User::ROLE_GERENTE,
            'administrador'
        );
    }

    public function view(User $user, Producto $producto): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(
            User::ROLE_GERENTE,
            'administrador'
        );
    }

    public function update(User $user, Producto $producto): bool
    {
        return $user->hasRole('administrador')
            || (
                $user->hasRole(User::ROLE_GERENTE)
                && $producto->usuario_id === $user->id
            );
    }

    public function delete(User $user, Producto $producto): bool
    {
        return $user->hasRole('administrador')
            || (
                $user->hasRole(User::ROLE_GERENTE)
                && $producto->usuario_id === $user->id
            );
    }
}

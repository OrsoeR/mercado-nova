<?php

namespace App\Policies;

use App\Models\Categoria;
use App\Models\User;

class CategoriaPolicy
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

    public function view(User $user, Categoria $categoria): bool
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

    public function update(User $user, Categoria $categoria): bool
    {
        return $user->hasRole(
            User::ROLE_GERENTE,
            'administrador'
        );
    }

    public function delete(User $user, Categoria $categoria): bool
    {
        return $user->hasRole('administrador');
    }
}
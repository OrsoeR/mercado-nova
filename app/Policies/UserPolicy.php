<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determina si el usuario puede ver la lista de usuarios.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(
            User::ROLE_GERENTE,
            'administrador'
        );
    }

    /**
     * Determina si puede consultar un usuario.
     */
    public function view(User $user, User $model): bool
    {
        return $user->hasRole(
            User::ROLE_GERENTE,
            'administrador'
        );
    }

    /**
     * Determina si puede crear usuarios.
     */
    public function create(User $user): bool
    {
    return $user->hasRole(
        User::ROLE_GERENTE,
        User::ROLE_ADMINISTRADOR
    );
    }

    /**
     * Determina si puede actualizar un usuario.
     */
    public function update(User $user, User $model): bool
    {
    if ($user->isAdministrador()) {
        return true;
    }

    return $user->isGerente()
        && in_array(
            $model->role,
            [User::ROLE_CLIENTE, User::ROLE_EMPLEADO],
            true
        );
    }

    /**
     * Determina si puede eliminar usuarios.
     */
    public function delete(User $user, User $model): bool
    {
        if ($user->id === $model->id) {
            return false;
        }

        if ($user->hasRole('administrador')) {
            return true;
        }

        return $user->hasRole(User::ROLE_GERENTE)
            && $model->role === User::ROLE_CLIENTE;
    }
}

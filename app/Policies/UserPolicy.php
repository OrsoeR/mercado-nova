<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(
            User::ROLE_GERENTE,
            User::ROLE_ADMINISTRADOR
        );
    }

    public function view(User $user, User $model): bool
    {
        return $user->hasRole(
            User::ROLE_GERENTE,
            User::ROLE_ADMINISTRADOR
        );
    }

    /**
     * Administrador y Gerente pueden crear usuarios.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(
            User::ROLE_GERENTE,
            User::ROLE_ADMINISTRADOR
        );
    }

    /**
     * El Administrador puede editar cualquier usuario.
     * El Gerente puede editar Clientes y Empleados,
     * pero no Gerentes ni Administradores.
     */
    public function update(User $user, User $model): bool
    {
        if ($user->isAdministrador()) {
            return true;
        }

        return $user->isGerente()
            && $user->id !== $model->id
            && in_array(
                $model->role,
                [
                    User::ROLE_CLIENTE,
                    User::ROLE_EMPLEADO,
                ],
                true
            );
    }

    /**
     * El Administrador puede eliminar otras cuentas.
     * El Gerente solamente puede eliminar Clientes.
     */
    public function delete(User $user, User $model): bool
    {
        if ($user->id === $model->id) {
            return false;
        }

        if ($user->isAdministrador()) {
            return true;
        }

        return $user->isGerente()
            && $model->isCliente();
    }
}

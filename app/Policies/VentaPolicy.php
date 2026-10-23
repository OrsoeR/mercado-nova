<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Venta;

class VentaPolicy
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

    public function view(User $user, Venta $venta): bool
    {
        if ($user->hasRole('administrador')) {
            return true;
        }

        return $venta->cliente_id === $user->id
            || $venta->vendedor_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(
            User::ROLE_CLIENTE,
            User::ROLE_GERENTE,
            'administrador'
        );
    }

    public function update(User $user, Venta $venta): bool
    {
        return $user->hasRole('administrador');
    }

    public function delete(User $user, Venta $venta): bool
    {
        return $user->hasRole('administrador');
    }
}

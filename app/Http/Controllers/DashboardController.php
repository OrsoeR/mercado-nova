<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Redirige al usuario a su panel segun su rol.
     */
    public function redirect(Request $request): RedirectResponse
    {
        return redirect()->route(
            $request->user()->dashboardRoute()
        );
    }

    /**
     * Panel del administrador.
     */
    public function administrador(): View
    {
        return view('dashboards.administrador', [
            'totalUsuarios' => User::count(),
            'totalClientes' => User::where(
                'role',
                User::ROLE_CLIENTE
            )->count(),
            'totalEmpleados' => User::where(
                'role',
                User::ROLE_EMPLEADO
            )->count(),
            'totalGerentes' => User::where(
                'role',
                User::ROLE_GERENTE
            )->count(),
            'totalAdministradores' => User::where(
                'role',
                User::ROLE_ADMINISTRADOR
            )->count(),
        ]);
    }

    /**
     * Panel del cliente.
     */
    public function cliente(): View
    {
        return view('dashboards.cliente');
    }

    /**
     * Panel del empleado.
     */
    public function empleado(): View
    {
        return view('dashboards.empleado', [
            'totalClientes' => User::where(
                'role',
                User::ROLE_CLIENTE
            )->count(),
        ]);
    }

    /**
     * Panel del gerente.
     */
    public function gerente(): View
    {
        return view('dashboards.gerente', [
            'totalUsuarios' => User::count(),
            'totalClientes' => User::where(
                'role',
                User::ROLE_CLIENTE
            )->count(),
            'totalEmpleados' => User::where(
                'role',
                User::ROLE_EMPLEADO
            )->count(),
            'totalGerentes' => User::where(
                'role',
                User::ROLE_GERENTE
            )->count(),
            'recientes' => User::latest()->take(5)->get(),
        ]);
    }
}

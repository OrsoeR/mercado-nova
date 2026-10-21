<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): View
    {
        $this->authorize('viewAny', User::class);

        $search = trim((string) $request->query('buscar'));
        $role = $request->query('rol');

        $users = User::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when(
                $role !== null && array_key_exists($role, User::ROLES),
                fn ($q) => $q->where('role', $role)
            )
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $totals = User::selectRaw('role, COUNT(*) as total')
            ->groupBy('role')
            ->pluck('total', 'role');

        return view('usuarios.index', [
            'users' => $users,
            'totals' => $totals,
            'roles' => User::ROLES,
            'search' => $search,
            'role' => $role,
        ]);
    }

    /**
     * Mostrar el formulario para crear usuarios.
     */
    public function create(): View
    {
        $this->authorize('create', User::class);

        return view('usuarios.create', [
            'roles' => User::ROLES,
        ]);
    }

    /**
     * Guardar un usuario nuevo.
     */
    public function store(UserRequest $request): RedirectResponse
    {
        $this->authorize('create', User::class);

        User::create($request->validated());

        return redirect()
            ->route('usuarios.index')
            ->with('success', 'Usuario creado correctamente.');
    }

    /**
     * Mostrar los detalles de un usuario.
     */
    public function show(User $usuario): View
    {
        $this->authorize('view', $usuario);

        return view('usuarios.show', [
            'user' => $usuario,
        ]);
    }

    /**
     * Mostrar el formulario para editar un usuario.
     */
    public function edit(User $usuario): View
    {
        $this->authorize('update', $usuario);

        return view('usuarios.edit', [
            'user' => $usuario,
            'roles' => User::ROLES,
        ]);
    }

    /**
     * Actualizar un usuario.
     */
    public function update(
        UserRequest $request,
        User $usuario
    ): RedirectResponse {
        $this->authorize('update', $usuario);

        $data = $request->validated();

        // Si la contraseÃ±a estÃ¡ vacÃ­a, conservar la actual.
        if (empty($data['password'])) {
            unset($data['password']);
        }

        // Evitar que un gerente cambie su propio rol.
        if (
            $usuario->is($request->user())
            && $request->user()->isGerente()
            && isset($data['role'])
            && $data['role'] !== User::ROLE_GERENTE
        ) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'No puedes quitarte tu propio rol de gerente.'
                );
        }

        $usuario->update($data);

        return redirect()
            ->route('usuarios.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * Eliminar un usuario.
     */
    public function destroy(
        Request $request,
        User $usuario
    ): RedirectResponse {
        $this->authorize('delete', $usuario);

        if ($usuario->is($request->user())) {
            return back()->with(
                'error',
                'No puedes eliminar tu propia cuenta.'
            );
        }

        $usuario->delete();

        return redirect()
            ->route('usuarios.index')
            ->with('success', 'Usuario eliminado correctamente.');
    }
}

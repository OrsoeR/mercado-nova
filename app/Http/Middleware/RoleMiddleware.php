<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * 
 * Restringe una ruta a uno o más roles.
 * Uso en web.php:  ->middleware('role:gerente')  o  ->middleware('role:empleado,gerente')
 * 
 */
class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if (! $user->hasRole(...$roles)) {
            // Si intenta entrar a un área que no es suya, lo regresamos a su propio dashboard
            return redirect()
                ->route($user->dashboardRoute())
                ->with('error', 'No tienes permiso para acceder a esa sección.');
        }

        return $next($request);
    }
}

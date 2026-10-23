<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * Muestra el formulario de inicio de sesión.
     */
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    /**
     * Verifica las credenciales e inicia la sesión.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Escribe un correo electrónico válido.',
            'password.required' => 'La contraseña es obligatoria.',
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            Log::channel('autenticacion')->warning(
                'Intento de inicio de sesión fallido',
                [
                    'email' => $credentials['email'],
                    'ip' => $request->ip(),
                    'fecha' => now()->toDateTimeString(),
                ]
            );

            throw ValidationException::withMessages([
                'email' => 'El correo o la contraseña son incorrectos.',
            ]);
        }

        $request->session()->regenerate();

        $user = Auth::user();

        Log::channel('autenticacion')->info(
            'Inicio de sesión correcto',
            [
                'user_id' => $user->id,
                'email' => $user->email,
                'rol' => $user->role,
                'ip' => $request->ip(),
                'fecha' => now()->toDateTimeString(),
            ]
        );

        return redirect()
            ->route($user->dashboardRoute())
            ->with('success', "Bienvenido, {$user->name}.");
    }

    /**
     * Cierra la sesión del usuario.
     */
    public function logout(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user) {
            Log::channel('autenticacion')->info(
                'Cierre de sesión',
                [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'rol' => $user->role,
                    'ip' => $request->ip(),
                    'fecha' => now()->toDateTimeString(),
                ]
            );
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('home')
            ->with('success', 'Cerraste sesión correctamente.');
    }
}
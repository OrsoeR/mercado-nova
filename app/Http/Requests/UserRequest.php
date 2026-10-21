<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        if (!$user) {
            return false;
        }

        // El administrador puede gestionar todos los usuarios.
        if ($user->isAdministrador()) {
            return true;
        }

        // El gerente puede gestionar clientes y empleados.
        if ($user->isGerente()) {
            $usuarioObjetivo = $this->route('usuario');

            // Para crear usuarios, solo puede asignar cliente o empleado.
            if (!$usuarioObjetivo instanceof User) {
                return in_array(
                    $this->input('role'),
                    [
                        User::ROLE_CLIENTE,
                        User::ROLE_EMPLEADO,
                    ],
                    true
                );
            }

            // Para editar, solo puede gestionar clientes y empleados,
            // manteniendo esos mismos roles permitidos.
            return in_array(
                $usuarioObjetivo->role,
                [
                    User::ROLE_CLIENTE,
                    User::ROLE_EMPLEADO,
                ],
                true
            ) && in_array(
                $this->input('role', $usuarioObjetivo->role),
                [
                    User::ROLE_CLIENTE,
                    User::ROLE_EMPLEADO,
                ],
                true
            );
        }

        // Clientes y empleados no pueden gestionar usuarios.
        return false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim((string) $this->name),
            'email' => strtolower(trim((string) $this->email)),
            'phone' => $this->phone
                ? preg_replace('/\D/', '', $this->phone)
                : null,
        ]);
    }

    public function rules(): array
    {
        $user = $this->route('usuario');
        $isUpdate = $user instanceof User;

        $rolesPermitidos = $this->user()?->isAdministrador()
            ? array_keys(User::ROLES)
            : [
                User::ROLE_CLIENTE,
                User::ROLE_EMPLEADO,
            ];

        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:100',
                'regex:/^[\pL\s]+$/u',
            ],

            'email' => [
                'required',
                'string',
                'email',
                'max:150',
                Rule::unique('users', 'email')
                    ->ignore($isUpdate ? $user->id : null),
            ],

            'phone' => ['nullable', 'digits:10'],

            'role' => [
                'required',
                Rule::in($rolesPermitidos),
            ],

            'password' => [
                $isUpdate ? 'nullable' : 'required',
                'confirmed',
                Password::min(8)->letters()->mixedCase()->numbers(),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.regex' => 'El nombre solo puede contener letras y espacios.',
            'email.unique' => 'Ya existe una cuenta con este correo.',
            'phone.digits' => 'El teléfono debe tener exactamente 10 dígitos.',
            'role.in' => 'Selecciona un rol permitido para tu cuenta.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ];
    }
}

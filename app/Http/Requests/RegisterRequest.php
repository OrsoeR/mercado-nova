<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

/**
 * Validación del registro público de usuarios.
 */
class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** Limpia espacios y normaliza el correo antes de validar. */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'name'  => trim((string) $this->name),
            'email' => strtolower(trim((string) $this->email)),
            'phone' => $this->phone ? preg_replace('/\D/', '', $this->phone) : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'min:3', 'max:100', 'regex:/^[\pL\s]+$/u'],
            'email'    => ['required', 'string', 'email', 'max:150', 'unique:users,email'],
            'phone'    => ['nullable', 'digits:10'],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)->letters()->mixedCase()->numbers(),
            ],
            'terms'    => ['accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'      =>  'El nombre es obligatorio.',
            'name.min'           =>  'El nombre debe tener al menos 3 caracteres.',
            'name.max'           =>  'El nombre no puede pasar de 100 caracteres.',
            'name.regex'         =>  'El nombre solo puede contener letras y espacios.',
            'email.required'     =>  'El correo electrónico es obligatorio.',
            'email.email'        =>  'Escribe un correo electrónico válido.',
            'email.unique'       =>  'Ya existe una cuenta con este correo.',
            'phone.digits'       =>  'El teléfono debe tener exactamente 10 dígitos.',
            'password.required'  =>  'La contraseña es obligatoria.',
            'password.confirmed' =>  'Las contraseñas no coinciden.',
            'password.min'       =>  'La contraseña debe tener al menos 8 caracteres.',
            // Los mensajes de letras/mayúsculas/números están en lang/es/validation.php
            'terms.accepted'     => 'Debes aceptar los términos y condiciones.',
        ];
    }
}

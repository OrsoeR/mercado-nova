<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductoRequest extends FormRequest
{
    public function authorize(): bool
    {
        $producto = $this->route('producto');

        return $producto !== null
            && ($this->user()?->can('update', $producto) ?? false);
    }

    public function rules(): array
    {
        return [
            'nombre' => ['sometimes', 'required', 'string', 'max:150'],
            'descripcion' => ['sometimes', 'required', 'string', 'max:2000'],
            'precio' => ['sometimes', 'required', 'numeric', 'min:0.01', 'max:99999999.99'],
            'existencia' => ['sometimes', 'required', 'integer', 'min:0', 'max:2147483647'],
            'categorias' => ['sometimes', 'array'],
            'categorias.*' => ['integer', 'exists:categorias,id'],
        ];
    }
}
